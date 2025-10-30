/**
 * Real-time Notifications Handler
 * Handles Laravel Echo events for real-time notifications
 */

class NotificationHandler {
    constructor() {
        this.init();
    }

    init() {
        if (typeof window.Echo === 'undefined') {
            console.warn('Laravel Echo is not initialized');
            return;
        }

        this.setupUserNotifications();
        this.setupPublicNotifications();
        this.setupToastNotifications();
    }

    /**
     * Setup private user notifications
     */
    setupUserNotifications() {
        const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        if (!userId) {
            return; // User not authenticated
        }

        // Listen for application status changes
        window.Echo.private(`user.${userId}`)
            .listen('ApplicationStatusChanged', (e) => {
                this.showNotification({
                    title: 'Application Update',
                    message: e.message,
                    type: 'info',
                    data: e
                });
                
                // Update UI if on applications page
                this.updateApplicationStatus(e);
            })
            .listen('CertificateGenerated', (e) => {
                this.showNotification({
                    title: 'New Certificate',
                    message: e.message,
                    type: 'success',
                    data: e
                });
                
                // Update certificates count
                this.updateCertificatesCount();
            });
    }

    /**
     * Setup public event notifications
     */
    setupPublicNotifications() {
        // Listen for new events published
        window.Echo.channel('events')
            .listen('EventPublished', (e) => {
                this.showNotification({
                    title: 'New Volunteer Opportunity',
                    message: e.message,
                    type: 'info',
                    data: e
                });
                
                // Update events list if on events page
                this.updateEventsList(e);
            });
    }

    /**
     * Show toast notification
     */
    showNotification({ title, message, type = 'info', data = null }) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 transform translate-x-full transition-transform duration-300 ${this.getNotificationClasses(type)}`;
        
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${this.getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="mt-1 text-sm text-gray-500">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);

        // Play notification sound (optional)
        this.playNotificationSound();
    }

    /**
     * Get notification CSS classes based on type
     */
    getNotificationClasses(type) {
        const classes = {
            'success': 'border-green-400',
            'error': 'border-red-400',
            'warning': 'border-yellow-400',
            'info': 'border-blue-400'
        };
        return classes[type] || classes.info;
    }

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(type) {
        const icons = {
            'success': `<svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>`,
            'error': `<svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>`,
            'warning': `<svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>`,
            'info': `<svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>`
        };
        return icons[type] || icons.info;
    }

    /**
     * Update application status in UI
     */
    updateApplicationStatus(data) {
        const applicationRow = document.querySelector(`[data-application-id="${data.application_id}"]`);
        if (applicationRow) {
            const statusElement = applicationRow.querySelector('.application-status');
            if (statusElement) {
                statusElement.textContent = data.status;
                statusElement.className = `application-status px-2 py-1 text-xs rounded-full ${this.getStatusClasses(data.status)}`;
            }
        }
    }

    /**
     * Update certificates count
     */
    updateCertificatesCount() {
        const countElement = document.querySelector('.certificates-count');
        if (countElement) {
            const currentCount = parseInt(countElement.textContent) || 0;
            countElement.textContent = currentCount + 1;
        }
    }

    /**
     * Update events list with new event
     */
    updateEventsList(data) {
        const eventsList = document.querySelector('.events-list');
        if (eventsList) {
            // Add new event to the top of the list
            const eventElement = this.createEventElement(data);
            eventsList.insertBefore(eventElement, eventsList.firstChild);
        }
    }

    /**
     * Create event element for new events
     */
    createEventElement(data) {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200';
        eventDiv.innerHTML = `
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">${data.title}</h3>
                    <p class="text-sm text-gray-600 mb-2">by ${data.organization}</p>
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span>📅 ${new Date(data.start_date).toLocaleDateString()}</span>
                        <span>📍 ${data.location}</span>
                        <span>👥 ${data.volunteers_needed} volunteers needed</span>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    New
                </span>
            </div>
            <div class="mt-4">
                <a href="${data.url}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    View Details
                </a>
            </div>
        `;
        return eventDiv;
    }

    /**
     * Get status CSS classes
     */
    getStatusClasses(status) {
        const classes = {
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'completed': 'bg-blue-100 text-blue-800'
        };
        return classes[status] || classes.pending;
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        // Create a subtle notification sound
        if ('AudioContext' in window || 'webkitAudioContext' in window) {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                
                gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.01);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2);
            } catch (error) {
                // Ignore audio errors
            }
        }
    }

    /**
     * Setup toast notifications container
     */
    setupToastNotifications() {
        // Ensure we have a notifications container
        if (!document.querySelector('#notifications-container')) {
            const container = document.createElement('div');
            container.id = 'notifications-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }
}

// Initialize notifications when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Echo to be initialized
    setTimeout(() => {
        new NotificationHandler();
    }, 1000);
});

// Export for manual initialization if needed
window.NotificationHandler = NotificationHandler;