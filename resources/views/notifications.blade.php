@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Notifications</h1>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Home
            </a>
        </div>

        <!-- No Notifications Yet -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-4">
                <svg class="w-24 h-24 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">No notifications yet</h2>
            <p class="text-gray-500">When you get notifications, they'll show up here.</p>
        </div>

        <!-- Example notification structure (commented out) -->
        <!--
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Notification Title</h3>
                        <p class="text-sm text-gray-600 mt-1">Notification message goes here...</p>
                        <span class="text-xs text-gray-400 mt-2 block">2 hours ago</span>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
</div>
@endsection