@extends('layouts.app')

@section('title', $recipe['title'] . ' ' . $recipe['highlight'] . ' - MealMatch')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- 1. Hero Section --}}
    <div class="relative w-full h-96 rounded-3xl overflow-hidden shadow-xl mb-8">
        <img src="{{ $recipe['image'] }}" alt="{{ $recipe['title'] }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        
        <div class="absolute top-8 left-8 text-white">
            <p class="text-sm font-medium tracking-wider uppercase mb-2 opacity-90">{{ $recipe['subtitle'] }}</p>
            <h1 class="text-5xl font-extrabold leading-tight">
                {{ $recipe['title'] }} <span class="text-orange-500">{{ $recipe['highlight'] }}</span>
            </h1>
        </div>

        {{-- Favorite Button --}}
        <button class="absolute top-8 right-8 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg text-gray-400 hover:text-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </button>
    </div>

    {{-- 2. Quick Info Bar --}}
    <div class="flex flex-wrap items-center justify-between bg-white/70 backdrop-blur-sm rounded-3xl shadow p-6 mb-8 gap-4">
        {{-- Cuisine --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.125 1.125 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.607a.875.875 0 011.121-1.033l.447.149c.386.128.703.403.89.771l.24.46c.43.816-.274 1.827-1.257 1.827h-.502m-3.384-3.248a.75.75 0 00-1.06-1.06l-2.25 2.25a.75.75 0 001.06 1.06l2.25-2.25zm4.26-1.313a.75.75 0 00-1.06-1.06l-2.25 2.25a.75.75 0 001.06 1.06l2.25-2.25z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Cuisine</p>
                <p class="text-gray-900 font-bold">{{ $recipe['cuisine'] }}</p>
            </div>
        </div>
        {{-- Servings --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Servings</p>
                <p class="text-gray-900 font-bold">{{ $recipe['servings'] }}</p>
            </div>
        </div>
        {{-- Prep Time --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Prep Time</p>
                <p class="text-gray-900 font-bold">{{ $recipe['prep_time'] }}</p>
            </div>
        </div>
        {{-- Cook Time --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.638 5.214M8.25 6.75h7.5M10.5 9h3m-3 2.25h3m-3 2.25h3" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Cook Time</p>
                <p class="text-gray-900 font-bold">{{ $recipe['cook_time'] }}</p>
            </div>
        </div>
        {{-- Difficulty --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Difficulty</p>
                <p class="text-gray-900 font-bold">{{ $recipe['difficulty'] }}</p>
            </div>
        </div>
    </div>

    {{-- 3. Description & Author --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        {{-- Description Column --}}
        <div class="lg:col-span-2">
            <p class="text-gray-600 leading-relaxed mb-6">
                {{ $recipe['description'] }}
            </p>
            {{-- Tags --}}
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="text-sm font-bold text-gray-900 mr-2">Tags:</span>
                @foreach($recipe['tags'] as $tag)
                    <span class="text-sm text-orange-500 font-medium">{{ $tag }}@if(!$loop->last),@endif</span>
                @endforeach
            </div>
        </div>
        
        {{-- Author Column --}}
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow p-6 flex items-center gap-4 h-fit">
            <img src="{{ $recipe['author']['image'] }}" alt="{{ $recipe['author']['name'] }}" class="w-20 h-20 rounded-full object-cover shadow-sm">
            <div>
                <p class="text-xs text-gray-500 font-medium">Recipe by</p>
                <p class="text-lg font-bold text-gray-900">{{ $recipe['author']['name'] }}</p>
                <a href="#" class="text-sm text-orange-500 font-medium hover:underline">See More</a>
            </div>
        </div>
    </div>

    {{-- 4. Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column (Ingredients & Instructions) --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Ingredients Card --}}
            <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Ingredients</h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8 list-disc list-inside marker:text-orange-500 text-gray-700 font-medium">
                    @foreach($recipe['ingredients'] as $ingredient)
                        <li>{{ $ingredient }}</li>
                    @endforeach
                </ul>
            </div>
            
            {{-- Instructions Section --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Cooking <span class="text-orange-500">Instructions</span></h2>
                <div class="space-y-4">
                    @foreach($recipe['instructions'] as $instruction)
                    <div class="flex gap-6 bg-white/50 rounded-3xl p-6">
                        {{-- Step Number --}}
                        <div class="text-4xl font-extrabold text-orange-300 shrink-0 select-none">
                            {{ $instruction['step'] }}
                        </div>
                        {{-- Text --}}
                        <p class="text-gray-700 leading-relaxed py-2">{{ $instruction['text'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column (Nutrition & Ad) --}}
        <div class="space-y-8">
            {{-- Nutritional Info Card --}}
            <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Nutritional Info</h2>
                <ul class="space-y-4">
                    @foreach($recipe['nutrition'] as $item)
                    <li class="flex justify-between items-center text-gray-700 font-medium">
                        <span>{{ $item['label'] }}</span>
                        <span>{{ $item['value'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            
            {{-- Side Image/Ad Card --}}
            <div class="rounded-3xl overflow-hidden shadow-lg h-[400px] relative group">
                 <img src="https://images.unsplash.com/photo-1606787366850-de6330128bfc?q=80&w=600&auto=format&fit=crop" alt="Enjoy your meal" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                 <div class="absolute inset-0 bg-black/20"></div>
                 <div class="absolute bottom-6 left-6 text-white">
                     <span class="text-orange-400 font-bold text-xl">MealMatch</span>
                     <h3 class="text-2xl font-bold">Find Your Next <br>Favorite Meal.</h3>
                 </div>
            </div>
        </div>
    </div>

</div>
@endsection