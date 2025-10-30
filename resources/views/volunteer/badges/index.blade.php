@extends('volunteer.layouts.app')

@section('title', 'My Badges')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Badges</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($badges as $badge)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-800">{{ $badge->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $badge->category }}</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 mb-4">{{ $badge->description }}</p>
                    
                    @if($badge->progress->first())
                        @if($badge->progress->first()->earned_at)
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm inline-block">
                                Earned on {{ $badge->progress->first()->earned_at->format('M d, Y') }}
                            </div>
                        @else
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $badge->progress->first()->progress }}/{{ $badge->required_amount }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ min(100, ($badge->progress->first()->progress / $badge->required_amount) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="mb-2">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Progress</span>
                                <span>0/{{ $badge->required_amount }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                    
                    <a href="{{ route('volunteer.badges.show', $badge) }}" 
                       class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                        View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No badges available at this time.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection