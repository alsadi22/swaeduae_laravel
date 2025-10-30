@extends('admin.layouts.app')

@section('title', 'Edit Organization')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Organization</h1>
                        <p class="mt-1 text-sm text-gray-600">Update organization information</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.organizations.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-900 transition-colors">
                            Back to Organizations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.organizations.update', $organization) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Organization Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $organization->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $organization->phone) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $organization->address) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $organization->city) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Emirate -->
                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700">Emirate</label>
                        <select name="emirate" id="emirate" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Select Emirate</option>
                            <option value="Abu Dhabi" {{ (old('emirate', $organization->emirate) == 'Abu Dhabi') ? 'selected' : '' }}>Abu Dhabi</option>
                            <option value="Dubai" {{ (old('emirate', $organization->emirate) == 'Dubai') ? 'selected' : '' }}>Dubai</option>
                            <option value="Sharjah" {{ (old('emirate', $organization->emirate) == 'Sharjah') ? 'selected' : '' }}>Sharjah</option>
                            <option value="Ajman" {{ (old('emirate', $organization->emirate) == 'Ajman') ? 'selected' : '' }}>Ajman</option>
                            <option value="Umm Al Quwain" {{ (old('emirate', $organization->emirate) == 'Umm Al Quwain') ? 'selected' : '' }}>Umm Al Quwain</option>
                            <option value="Ras Al Khaimah" {{ (old('emirate', $organization->emirate) == 'Ras Al Khaimah') ? 'selected' : '' }}>Ras Al Khaimah</option>
                            <option value="Fujairah" {{ (old('emirate', $organization->emirate) == 'Fujairah') ? 'selected' : '' }}>Fujairah</option>
                        </select>
                        @error('emirate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="pending" {{ (old('status', $organization->status) == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ (old('status', $organization->status) == 'approved') ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ (old('status', $organization->status) == 'rejected') ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                        Update Organization
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection