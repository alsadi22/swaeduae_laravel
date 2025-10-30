@extends('layouts.public')

@section('title', $page->meta_title ?? $page->title)

@section('meta-description', $page->meta_description)
@section('meta-keywords', $page->meta_keywords)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
                
                <div class="prose max-w-none">
                    {!! $page->content ?? '<p class="text-gray-500">No content available.</p>' !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection