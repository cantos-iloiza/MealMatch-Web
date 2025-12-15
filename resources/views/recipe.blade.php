@extends('layouts.app')

@section('title', 'Recipes - MealMatch')

@section('content')

{{-- 1. Hero Section --}}
<div class="relative w-full h-72 rounded-3xl overflow-hidden shadow-lg mb-8 group">
    <img src="{{ asset('images/recipe-header.jpg') }}" 
         onerror="this.src='https://images.unsplash.com/photo-1556910103-1c02745a30bf?q=80&w=2000&auto=format&fit=crop'"
         alt="Cooking Header" 
         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10 text-center px-4">
        <h1 class="text-5xl font-bold mb-3 tracking-wide drop-shadow-lg">Kitchen Creations</h1>
        <p class="text-xl font-light opacity-90 max-w-2xl">
            Explore thousands of recipes or find the perfect meal for your ingredients.
        </p>
    </div>
</div>

{{-- 2. TABS: Discovery vs Favorites --}}
<div class="flex items-center justify-center mb-10">
    <div class="bg-gray-100 p-1.5 rounded-2xl flex items-center gap-1 shadow-inner">
        {{-- Discovery Tab --}}
        <a href="{{ route('recipes.index', ['tab' => 'discovery']) }}" 
           class="px-8 py-3 rounded-xl text-sm font-bold transition-all {{ $currentTab == 'discovery' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
            🌍 Discovery
        </a>
        
        {{-- Favorites Tab --}}
        <a href="{{ route('recipes.index', ['tab' => 'favorites']) }}" 
           class="px-8 py-3 rounded-xl text-sm font-bold transition-all {{ $currentTab == 'favorites' ? 'bg-white text-orange-500 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
            ❤️ Favorites
        </a>
    </div>
</div>

{{-- 3. Search & Filter Bar (Only show on Discovery Tab) --}}
@if($currentTab == 'discovery')
<div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-10">
    {{-- Search Input --}}
    <form action="{{ route('recipes.index') }}" method="GET" class="relative w-full md:w-80 group">
        <input type="hidden" name="tab" value="discovery"> {{-- Keep tab active --}}
        <input type="text" 
               name="search" 
               value="{{ request('search') }}"
               placeholder="Search recipes..." 
               class="w-full pl-12 pr-4 py-3 rounded-2xl border-2 border-transparent bg-white shadow-sm focus:outline-none focus:border-orange-400 focus:ring-0 transition-all text-gray-700 placeholder-gray-400">
        <svg class="w-6 h-6 text-gray-400 absolute left-4 top-3.5 group-focus-within:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </form>

    {{-- Categories --}}
    <div class="flex-1 w-full overflow-x-auto pb-2 scrollbar-hide">
        <div class="flex items-center justify-start md:justify-end gap-3 px-2">
            
            {{-- 'All' Button --}}
            <a href="{{ route('recipes.index', ['tab' => 'discovery']) }}" 
               class="px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm whitespace-nowrap transition-all transform hover:-translate-y-0.5
                      {{ !request('category') && !request('search') ? 'bg-orange-500 text-white shadow-orange-200' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600' }}">
                All
            </a>
            
            {{-- Category Pills --}}
            @foreach(['Breakfast', 'Chicken', 'Beef', 'Seafood', 'Vegetarian', 'Pasta'] as $cat)
                <a href="{{ route('recipes.index', ['tab' => 'discovery', 'category' => $cat]) }}" 
                   class="px-6 py-2.5 rounded-xl text-sm font-semibold shadow-sm whitespace-nowrap transition-all transform hover:-translate-y-0.5
                          {{ request('category') == $cat ? 'bg-orange-500 text-white shadow-orange-200' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- 4. Recipe Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
    @forelse($recipes as $recipe)
    <a href="{{ route('recipe.show', ['id' => $recipe['idMeal']]) }}" 
       class="group relative h-[22rem] bg-white rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
        
        <div class="h-full w-full relative overflow-hidden">
            <img src="{{ $recipe['strMealThumb'] }}" 
                 alt="{{ $recipe['strMeal'] }}" 
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-90"></div>
        </div>

        <div class="absolute bottom-0 left-0 w-full p-6">
            <span class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-wider text-white uppercase bg-orange-500 rounded-lg shadow-sm">
                {{ $recipe['strCategory'] ?? request('category', 'Recipe') }}
            </span>
            <h3 class="text-2xl font-bold text-white leading-tight drop-shadow-md line-clamp-2 mb-1 group-hover:text-orange-100 transition-colors">
                {{ $recipe['strMeal'] }}
            </h3>
        </div>

        {{-- Heart Button (Corrected Logic) --}}
        <button onclick="toggleFavorite(this, event)"
                data-id="{{ $recipe['idMeal'] }}"
                data-title="{{ $recipe['strMeal'] }}"
                data-image="{{ $recipe['strMealThumb'] }}"
                data-category="{{ $recipe['strCategory'] ?? '' }}"
                class="absolute top-4 right-4 w-10 h-10 rounded-full flex items-center justify-center transition-all z-10 shadow-sm
                       {{ in_array($recipe['idMeal'], $favoriteIds ?? []) ? 'bg-white text-red-500' : 'bg-white/30 backdrop-blur-md text-white hover:bg-white hover:text-red-500' }}">
            
            <svg class="w-5 h-5 transition-transform active:scale-75" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </button>
    </a>
    @empty
    {{-- Empty State --}}
    <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
        <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mb-6">
            <span class="text-4xl">{{ $currentTab == 'favorites' ? '💔' : '🥘' }}</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">
            {{ $currentTab == 'favorites' ? 'No favorites yet' : 'No recipes found' }}
        </h3>
        <p class="text-gray-500 max-w-md mx-auto mb-6">
            {{ $currentTab == 'favorites' ? 'Go to Discovery and save some recipes to see them here!' : 'Try a different keyword or category.' }}
        </p>
        <a href="{{ route('recipes.index', ['tab' => 'discovery']) }}" class="px-8 py-3 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-200">
            {{ $currentTab == 'favorites' ? 'Discover Recipes' : 'Clear Filters' }}
        </a>
    </div>
    @endforelse
</div>

{{-- Script to Handle Heart Clicks --}}
<script>
    async function toggleFavorite(btn, e) {
        e.preventDefault(); // Stop clicking the card link
        e.stopPropagation(); // Really stop it

        // Get data from attributes (Safe way)
        const id = btn.dataset.id;
        const title = btn.dataset.title;
        const image = btn.dataset.image;
        const category = btn.dataset.category;

        try {
            const response = await fetch("{{ route('recipe.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    recipe_id: id,
                    title: title,
                    image: image,
                    category: category
                })
            });

            const data = await response.json();

            // Toggle Visuals
            if (data.status === 'added') {
                btn.classList.remove('bg-white/30', 'text-white');
                btn.classList.add('bg-white', 'text-red-500');
            } else {
                btn.classList.add('bg-white/30', 'text-white');
                btn.classList.remove('bg-white', 'text-red-500');
                
                // If on favorites tab, remove the card immediately
                const urlParams = new URLSearchParams(window.location.search);
                if(urlParams.get('tab') === 'favorites') {
                    btn.closest('a').style.display = 'none';
                }
            }

        } catch (error) {
            console.error('Error:', error);
        }
    }
</script>

@endsection