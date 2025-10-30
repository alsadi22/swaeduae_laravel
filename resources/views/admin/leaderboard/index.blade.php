@extends('layouts.admin')

@section('title', 'Volunteer Leaderboard')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-bold text-gray-900">Volunteer Leaderboard</h1>
            <p class="mt-2 text-sm text-gray-700">Top volunteers based on points and hours contributed.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <select id="timeframe" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="all_time">All Time</option>
                <option value="monthly">This Month</option>
                <option value="weekly">This Week</option>
            </select>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- User Stats -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Your Stats</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Your Position</p>
                                <p class="text-2xl font-bold text-gray-900">#<span id="user-position">-</span></p>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Points</p>
                                <p class="text-2xl font-bold text-gray-900" id="user-points">-</p>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Volunteer Hours</p>
                                <p class="text-2xl font-bold text-gray-900" id="user-hours">-</p>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Badges</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-3 gap-4" id="user-badges">
                        <!-- Badges will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Top Volunteers</h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200" id="leaderboard-list">
                        <!-- Leaderboard items will be loaded here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load leaderboard data
    loadLeaderboard();
    
    // Timeframe change handler
    document.getElementById('timeframe').addEventListener('change', function() {
        loadLeaderboard(this.value);
    });
    
    function loadLeaderboard(timeframe = 'all_time') {
        fetch(`/api/leaderboard?timeframe=${timeframe}`)
            .then(response => response.json())
            .then(data => {
                // Update user stats
                document.getElementById('user-position').textContent = data.user_position;
                document.getElementById('user-points').textContent = data.user_points || 0;
                document.getElementById('user-hours').textContent = data.user_hours || 0;
                
                // Update leaderboard list
                const leaderboardList = document.getElementById('leaderboard-list');
                leaderboardList.innerHTML = '';
                
                data.leaderboard.forEach((user, index) => {
                    const listItem = document.createElement('li');
                    listItem.className = 'px-4 py-4 sm:px-6';
                    
                    // Highlight current user
                    if (user.id === {{ Auth::user()->id }}) {
                        listItem.className += ' bg-blue-50';
                    }
                    
                    listItem.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">${user.rank}</span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">${user.name}</p>
                                    <p class="text-sm text-gray-500">${user.email}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${user.points || 0} pts</p>
                                    <p class="text-sm text-gray-500">${user.total_volunteer_hours || 0} hrs</p>
                                </div>
                                ${user.rank <= 3 ? `
                                    <div class="flex-shrink-0">
                                        ${user.rank === 1 ? `
                                            <svg class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        ` : user.rank === 2 ? `
                                            <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        ` : `
                                            <svg class="h-6 w-6 text-yellow-700" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        `}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    
                    leaderboardList.appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('Error loading leaderboard:', error);
            });
    }
    
    // Load user badges
    fetch('/api/my-badges')
        .then(response => response.json())
        .then(data => {
            const badgesContainer = document.getElementById('user-badges');
            badgesContainer.innerHTML = '';
            
            if (data.data && data.data.length > 0) {
                data.data.forEach(badge => {
                    const badgeElement = document.createElement('div');
                    badgeElement.className = 'flex flex-col items-center';
                    badgeElement.innerHTML = `
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: ${badge.color || '#ccc'}">
                            <span class="text-xs font-bold text-white">★</span>
                        </div>
                        <p class="mt-1 text-xs text-center text-gray-500">${badge.name}</p>
                    `;
                    badgesContainer.appendChild(badgeElement);
                });
            } else {
                badgesContainer.innerHTML = '<p class="text-sm text-gray-500 text-center">No badges earned yet</p>';
            }
        })
        .catch(error => {
            console.error('Error loading badges:', error);
        });
});
</script>
@endsection