{{-- Real-time Notifications Component --}}
<div id="notifications-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
    {{-- Notifications will be dynamically added here by JavaScript --}}
</div>

{{-- Meta tags for JavaScript --}}
@auth
    <meta name="user-id" content="{{ auth()->id() }}">
@endauth

{{-- Notification Bell Icon (Optional) --}}
<div x-data="{ 
    unreadCount: 0,
    showDropdown: false,
    notifications: []
}" class="relative">
    <button @click="showDropdown = !showDropdown" 
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        {{-- Notification Badge --}}
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem] h-5">
        </span>
    </button>

    {{-- Notifications Dropdown --}}
    <div x-show="showDropdown" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showDropdown = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 pointer-events-auto">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                     :class="{ 'bg-blue-50': !notification.read }">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-600 rounded-full" x-show="!notification.read"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                            <p class="text-sm text-gray-600" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="notification.time"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-gray-200">
            <button @click="markAllAsRead()" 
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Mark all as read
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced notification handling with Alpine.js integration
    if (typeof window.Echo !== 'undefined' && document.querySelector('[x-data]')) {
        const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
        
        if (userId) {
            // Listen for real-time notifications and update Alpine.js data
            window.Echo.private(`user.${userId}`)
                .listen('ApplicationStatusChanged', (e) => {
                    addNotificationToDropdown({
                        id: Date.now(),
                        title: 'Application Update',
                        message: e.message,
                        time: 'Just now',
                        read: false,
                        type: 'application'
                    });
                })
                .listen('CertificateGenerated', (e) => {
                    addNotificationToDropdown({
                        id: Date.now(),
                        title: 'New Certificate',
                        message: e.message,
                        time: 'Just now',
                        read: false,
                        type: 'certificate'
                    });
                });
        }
    }

    function addNotificationToDropdown(notification) {
        // Find Alpine.js component and update notifications
        const notificationComponent = document.querySelector('[x-data]').__x?.$data;
        if (notificationComponent) {
            notificationComponent.notifications.unshift(notification);
            notificationComponent.unreadCount++;
        }
    }

    // Mark all notifications as read
    window.markAllAsRead = function() {
        const notificationComponent = document.querySelector('[x-data]').__x?.$data;
        if (notificationComponent) {
            notificationComponent.notifications.forEach(n => n.read = true);
            notificationComponent.unreadCount = 0;
        }
    };
});
</script>

<style>
/* Custom scrollbar for notifications dropdown */
.max-h-96::-webkit-scrollbar {
    width: 4px;
}

.max-h-96::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.max-h-96::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>