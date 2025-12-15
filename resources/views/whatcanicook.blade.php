@extends('layouts.app')

@section('title', 'What Can I Cook?')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">What Can I Cook?</h1>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Home
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-4">
                <svg class="w-24 h-24 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Recipe Suggestions Coming Soon!</h2>
            <p class="text-gray-500">We'll help you discover what you can cook based on your preferences.</p>
        </div>
    </div>
</div>
@endsection