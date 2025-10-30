@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Analytics Dashboard</h1>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow border">
                        <div class="flex items-center">
                            <div class="rounded-full bg-blue-100 p-3">
                                <i class="fas fa-users text-blue-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Users</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_users']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow border">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 p-3">
                                <i class="fas fa-building text-green-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Organizations</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_organizations']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow border">
                        <div class="flex items-center">
                            <div class="rounded-full bg-yellow-100 p-3">
                                <i class="fas fa-calendar-alt text-yellow-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Events</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_events']) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow border">
                        <div class="flex items-center">
                            <div class="rounded-full bg-purple-100 p-3">
                                <i class="fas fa-certificate text-purple-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Certificates</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_certificates']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trend Analysis Section -->
                <div class="bg-white p-6 rounded-lg shadow border mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Trend Analysis</h2>
                        <div class="flex space-x-2">
                            <select id="trend-period" class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option value="7days">Last 7 Days</option>
                                <option value="30days">Last 30 Days</option>
                                <option value="6months" selected="selected">Last 6 Months</option>
                                <option value="1year">Last Year</option>
                            </select>
                            <select id="trend-metric" class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option value="users">Users</option>
                                <option value="events">Events</option>
                                <option value="certificates">Certificates</option>
                                <option value="attendances">Attendance Records</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="trend-chart" height="100"></canvas>
                    </div>
                </div>

                <!-- Data Visualization Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- User Statistics -->
                    <div class="bg-white p-6 rounded-lg shadow border">
                        <h3 class="text-lg font-semibold mb-4">User Statistics</h3>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">{{ $userStats['admins'] }}</p>
                                <p class="text-sm text-gray-500">Admins</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $userStats['organizations'] }}</p>
                                <p class="text-sm text-gray-500">Organizations</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-purple-600">{{ $userStats['volunteers'] }}</p>
                                <p class="text-sm text-gray-500">Volunteers</p>
                            </div>
                        </div>
                        <div class="h-48">
                            <canvas id="user-stats-chart"></canvas>
                        </div>
                    </div>

                    <!-- Organization Statistics -->
                    <div class="bg-white p-6 rounded-lg shadow border">
                        <h3 class="text-lg font-semibold mb-4">Organization Statistics</h3>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-yellow-600">{{ $organizationStats['pending'] }}</p>
                                <p class="text-sm text-gray-500">Pending</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $organizationStats['approved'] }}</p>
                                <p class="text-sm text-gray-500">Approved</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-red-600">{{ $organizationStats['rejected'] }}</p>
                                <p class="text-sm text-gray-500">Rejected</p>
                            </div>
                        </div>
                        <div class="h-48">
                            <canvas id="org-stats-chart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Additional Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Event Statistics -->
                    <div class="bg-white p-6 rounded-lg shadow border">
                        <h3 class="text-lg font-semibold mb-4">Event Statistics</h3>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">{{ $eventStats['upcoming'] }}</p>
                                <p class="text-sm text-gray-500">Upcoming</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-yellow-600">{{ $eventStats['ongoing'] }}</p>
                                <p class="text-sm text-gray-500">Ongoing</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $eventStats['completed'] }}</p>
                                <p class="text-sm text-gray-500">Completed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Statistics -->
                    <div class="bg-white p-6 rounded-lg shadow border">
                        <h3 class="text-lg font-semibold mb-4">Certificate Statistics</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">{{ $certificateStats['this_month'] }}</p>
                                <p class="text-sm text-gray-500">This Month</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $certificateStats['this_year'] }}</p>
                                <p class="text-sm text-gray-500">This Year</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-purple-600">{{ $certificateStats['total'] }}</p>
                                <p class="text-sm text-gray-500">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize trend chart with data from backend
    var trendData = {
        dates: <?php echo json_encode($trendData['months']); ?>,
        users: <?php echo json_encode($trendData['users']); ?>,
        events: <?php echo json_encode($trendData['events']); ?>,
        certificates: <?php echo json_encode($trendData['certificates']); ?>,
        attendances: <?php echo json_encode($trendData['attendances']); ?>
    };

    // Create trend chart
    var trendCtx = document.getElementById('trend-chart').getContext('2d');
    var trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.dates,
            datasets: [{
                label: 'Users',
                data: trendData.users,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Trend filter functionality
    document.getElementById('trend-period').addEventListener('change', updateTrendChart);
    document.getElementById('trend-metric').addEventListener('change', updateTrendChart);

    function updateTrendChart() {
        var period = document.getElementById('trend-period').value;
        var metric = document.getElementById('trend-metric').value;

        fetch('<?php echo e(route('admin.analytics.trends')); ?>?period=' + period + '&metric=' + metric, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            trendChart.data.labels = data.dates;
            trendChart.data.datasets[0].label = metric.charAt(0).toUpperCase() + metric.slice(1);
            trendChart.data.datasets[0].data = data.values;
            trendChart.update();
        })
        .catch(error => console.error('Error:', error));
    }

    // User statistics chart
    var userStatsCtx = document.getElementById('user-stats-chart').getContext('2d');
    var userStatsChart = new Chart(userStatsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Admins', 'Organizations', 'Volunteers'],
            datasets: [{
                data: [<?php echo e($userStats['admins']); ?>, <?php echo e($userStats['organizations']); ?>, <?php echo e($userStats['volunteers']); ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Organization statistics chart
    var orgStatsCtx = document.getElementById('org-stats-chart').getContext('2d');
    var orgStatsChart = new Chart(orgStatsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [<?php echo e($organizationStats['pending']); ?>, <?php echo e($organizationStats['approved']); ?>, <?php echo e($organizationStats['rejected']); ?>],
                backgroundColor: [
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection