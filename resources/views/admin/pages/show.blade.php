@extends('layouts.admin')

@section('title', 'Page Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">{{ $page->title }}</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                onclick="return confirm('Are you sure you want to delete this page?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Slug</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->slug }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($page->is_published)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Draft
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Sort Order</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->sort_order }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Template</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->template ?? 'Default' }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->created_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                <div class="flex items-center">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->updated_at->format('M j, Y g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">SEO Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div class="flex items-start">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Meta Title</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->meta_title ?? 'Not set' }}</dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Meta Description</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->meta_description ?? 'Not set' }}</dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="w-32 text-sm font-medium text-gray-500">Meta Keywords</dt>
                                    <dd class="text-sm text-gray-900">{{ $page->meta_keywords ?? 'Not set' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="border rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Page Content</h3>
                    <div class="prose max-w-none">
                        {!! $page->content ?? '<p class="text-gray-500">No content available.</p>' !!}
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.pages.index') }}" class="text-blue-600 hover:text-blue-900">
                        ← Back to Pages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection