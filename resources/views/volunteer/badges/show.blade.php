@extends('volunteer.layouts.app')

@section('title', $badge->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $badge->name }}</h1>
        <a href="{{ route('volunteer.badges.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Badges
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row items-start mb-6">
                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-32 h-32 mb-4 md:mb-0 md:mr-6" />
                
                <div class="flex-1">
                    <div class="flex flex-wrap items-center mb-2">
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded mr-2">
                            {{ $badge->category }}
                        </span>
                        
                        @if($badge->progress->first() && $badge->progress->first()->earned_at)
                            <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                Earned
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 mb-4">{{ $badge->description }}</p>
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Requirements</h3>
                        <ul class="list-disc list-inside text-gray-600">
                            <li>Complete {{ $badge->required_amount }} {{ Str::plural('activity', $badge->required_amount) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            @if($badge->progress->first())
                @if($badge->progress->first()->earned_at)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-800 font-medium">
                                Congratulations! You earned this badge on {{ $badge->progress->first()->earned_at->format('F j, Y') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Your Progress</h3>
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>{{ $badge->progress->first()->progress }}/{{ $badge->required_amount }} completed</span>
                            <span>{{ round(($badge->progress->first()->progress / $badge->required_amount) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full" 
                                 style="width: {{ min(100, ($badge->progress->first()->progress / $badge->required_amount) * 100) }}%"></div>
                        </div>
                    </div>
                @endif
            @else
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Your Progress</h3>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>0/{{ $badge->required_amount }} completed</span>
                        <span>0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            @endif
            
            <div class="flex justify-end">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    View Related Activities
                </button>
            </div>
        </div>
    </div>
</div>
@endsection