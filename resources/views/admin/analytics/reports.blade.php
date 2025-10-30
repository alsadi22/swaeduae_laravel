@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Custom Report Builder</h1>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Report Configuration -->
                    <div class="lg:col-span-1">
                        <div class="bg-white p-6 rounded-lg shadow border">
                            <h2 class="text-xl font-semibold mb-4">Report Configuration</h2>
                            
                            <form id="report-form">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="report-type">
                                        Report Type
                                    </label>
                                    <select id="report-type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="user">User Statistics</option>
                                        <option value="organization">Organization Statistics</option>
                                        <option value="event">Event Statistics</option>
                                        <option value="certificate">Certificate Statistics</option>
                                        <option value="attendance">Attendance Statistics</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="date-range">
                                        Date Range
                                    </label>
                                    <select id="date-range" name="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month" selected>This Month</option>
                                        <option value="year">This Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                                
                                <div id="custom-date-range" class="hidden mb-4">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2" for="start-date">
                                                Start Date
                                            </label>
                                            <input type="date" id="start-date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2" for="end-date">
                                                End Date
                                            </label>
                                            <input type="date" id="end-date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Export Format
                                    </label>
                                    <div class="flex space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="format" value="pdf" checked>
                                            <span class="ml-2">PDF</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="format" value="excel">
                                            <span class="ml-2">Excel</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="format" value="csv">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between">
                                    <button type="button" id="generate-report" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Generate Report
                                    </button>
                                    <button type="button" id="schedule-report" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Schedule Report
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Saved Reports -->
                        <div class="bg-white p-6 rounded-lg shadow border mt-6">
                            <h2 class="text-xl font-semibold mb-4">Saved Reports</h2>
                            <ul class="space-y-2">
                                <li class="p-2 bg-gray-50 rounded flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">Monthly User Report</div>
                                        <div class="text-sm text-gray-500">PDF - Last run: 2 days ago</div>
                                    </div>
                                    <button class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </li>
                                <li class="p-2 bg-gray-50 rounded flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">Organization Statistics</div>
                                        <div class="text-sm text-gray-500">Excel - Last run: 1 week ago</div>
                                    </div>
                                    <button class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </li>
                            </ul>
                            <div class="mt-4">
                                <a href="{{ route('admin.scheduled-reports.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                                    <i class="fas fa-clock"></i> Manage Scheduled Reports
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Report Preview -->
                    <div class="lg:col-span-2">
                        <div class="bg-white p-6 rounded-lg shadow border">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold">Report Preview</h2>
                                <div class="space-x-2">
                                    <button id="export-report" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm focus:outline-none focus:shadow-outline">
                                        Export
                                    </button>
                                    <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm focus:outline-none focus:shadow-outline">
                                        Save
                                    </button>
                                </div>
                            </div>
                            
                            <div id="report-preview" class="border rounded p-4">
                                <div id="report-placeholder" class="text-center py-12">
                                    <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">Select report parameters and click "Generate Report" to preview data</p>
                                </div>
                                <div id="report-content" class="hidden">
                                    <h3 id="report-title" class="text-xl font-bold mb-4"></h3>
                                    <div id="report-metrics" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6"></div>
                                    <div id="report-chart" class="mb-6">
                                        <canvas id="report-chart-canvas" height="100"></canvas>
                                    </div>
                                    <div id="report-details"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Report Modal -->
<div id="schedule-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Schedule Report</h3>
                <button id="close-schedule-modal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="schedule-form" class="mt-4">
                @csrf
                <input type="hidden" name="type" id="schedule-type">
                <input type="hidden" name="format" id="schedule-format">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="schedule-name">
                        Report Name
                    </label>
                    <input type="text" id="schedule-name" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="schedule-frequency">
                        Frequency
                    </label>
                    <select id="schedule-frequency" name="frequency" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="schedule-time">
                        Time
                    </label>
                    <input type="time" id="schedule-time" name="time" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="schedule-recipients">
                        Recipients (comma separated emails)
                    </label>
                    <textarea id="schedule-recipients" name="recipients" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-schedule" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date-range');
    const customDateRange = document.getElementById('custom-date-range');
    const reportForm = document.getElementById('report-form');
    const scheduleModal = document.getElementById('schedule-modal');
    let chart = null;
    let currentReportData = null;
    
    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    });
    
    document.getElementById('generate-report').addEventListener('click', function() {
        generateReport();
    });
    
    document.getElementById('schedule-report').addEventListener('click', function() {
        openScheduleModal();
    });
    
    document.getElementById('export-report').addEventListener('click', function() {
        if (currentReportData) {
            exportReport();
        } else {
            alert('Please generate a report first');
        }
    });
    
    // Schedule modal events
    document.getElementById('close-schedule-modal').addEventListener('click', function() {
        scheduleModal.classList.add('hidden');
    });
    
    document.getElementById('cancel-schedule').addEventListener('click', function() {
        scheduleModal.classList.add('hidden');
    });
    
    document.getElementById('schedule-form').addEventListener('submit', function(e) {
        e.preventDefault();
        scheduleReport();
    });
    
    function generateReport() {
        // Show loading state
        document.getElementById('report-placeholder').classList.add('hidden');
        document.getElementById('report-content').classList.add('hidden');
        
        // Create loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-indicator';
        loadingDiv.className = 'text-center py-12';
        loadingDiv.innerHTML = `
            <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full text-blue-500 border-t-transparent mb-4"></div>
            <p class="text-gray-500">Generating report...</p>
        `;
        document.getElementById('report-preview').appendChild(loadingDiv);
        
        // Collect form data
        const formData = new FormData(reportForm);
        // Add format as json to get data for preview
        formData.set('format', 'json');
        
        // Make AJAX request
        fetch('{{ route("admin.analytics.generate-report") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading indicator
            document.getElementById('loading-indicator').remove();
            
            // Store current report data
            currentReportData = data;
            
            // Display report data
            displayReport(data);
        })
        .catch(error => {
            // Remove loading indicator
            document.getElementById('loading-indicator').remove();
            
            // Show error message
            document.getElementById('report-placeholder').classList.remove('hidden');
            document.getElementById('report-placeholder').innerHTML = `
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-red-500">Error generating report. Please try again.</p>
            `;
            console.error('Error:', error);
        });
    }
    
    function exportReport() {
        // Submit form for export
        const exportForm = document.createElement('form');
        exportForm.method = 'POST';
        exportForm.action = '{{ route("admin.analytics.export") }}';
        exportForm.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        exportForm.appendChild(csrfInput);
        
        // Add form data
        const formData = new FormData(reportForm);
        for (const [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            exportForm.appendChild(input);
        }
        
        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);
    }
    
    function openScheduleModal() {
        // Collect form data
        const type = document.getElementById('report-type').value;
        const format = document.querySelector('input[name="format"]:checked').value;
        
        // Set values in schedule form
        document.getElementById('schedule-type').value = type;
        document.getElementById('schedule-format').value = format;
        document.getElementById('schedule-name').value = type.charAt(0).toUpperCase() + type.slice(1) + ' Report';
        document.getElementById('schedule-time').value = '09:00';
        
        // Show modal
        scheduleModal.classList.remove('hidden');
    }
    
    function scheduleReport() {
        const formData = new FormData(document.getElementById('schedule-form'));
        
        fetch('{{ route("admin.scheduled-reports.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                scheduleModal.classList.add('hidden');
                alert('Report scheduled successfully!');
            } else {
                alert('Error scheduling report: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error scheduling report. Please try again.');
        });
    }
    
    function displayReport(data) {
        // Set report title
        document.getElementById('report-title').textContent = data.data.report_title;
        
        // Clear and populate metrics
        const metricsContainer = document.getElementById('report-metrics');
        metricsContainer.innerHTML = '';
        
        for (const [key, value] of Object.entries(data.data.metrics)) {
            const metricDiv = document.createElement('div');
            metricDiv.className = 'bg-gray-50 p-4 rounded';
            metricDiv.innerHTML = `
                <div class="text-sm text-gray-500">${key}</div>
                <div class="text-2xl font-bold">${value}</div>
            `;
            metricsContainer.appendChild(metricDiv);
        }
        
        // Create or update chart
        createChart(data.data.chart_data);
        
        // Show report content
        document.getElementById('report-content').classList.remove('hidden');
    }
    
    function createChart(chartData) {
        const ctx = document.getElementById('report-chart-canvas').getContext('2d');
        
        // Destroy existing chart if it exists
        if (chart) {
            chart.destroy();
        }
        
        // Create new chart
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Metrics',
                    data: chartData.values,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection