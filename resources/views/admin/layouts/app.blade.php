@extends('layouts.app')

@section('content')
    @include('admin.layouts.navigation')
    
    <!-- Page Content -->
    <div class="min-h-screen bg-gray-100">
        @yield('content')
    </div>
@endsection