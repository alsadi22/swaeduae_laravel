@extends('layouts.app')

@section('title', 'Test Layout')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900">Test Layout Page</h1>
        <p class="mt-4 text-gray-600">This is a test page to check if the app layout works without the EventController.</p>
        <p class="mt-2 text-gray-600">Current time: {{ now() }}</p>
    </div>
</div>
@endsection