@extends('layouts.app')

@section('title', 'Recipes - MealMatch')

@section('content')

{{-- 1. Hero Section --}}
<div class="relative w-full h-64 rounded-3xl overflow-hidden shadow-lg mb-8 group bg-gray-900">
    <img src="{{ asset('images/recipe-header.jpg') }}" 
         onerror="this.src='https://images.unsplash.com/photo-1556910103-1c02745a30bf?q=80&w=2000&auto=format&fit=crop'"
         alt="Cooking Header" 
         class="w-full h-full object-cover opacity-60">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10 text-center px-4">
        <h1 class="text-4xl font-bold mb-2 tracking-wide">Kitchen Creations</h1>
        <p class="text-lg font-light opacity-90">
            Explore thousands of recipes or find the perfect meal.
        </p>
    </div>
</div>

{{-- 2. TABS (Centered & Big) --}}
<div class="flex items-center justify-center gap-12 mb-10 border-b border-gray-200 px-4">
    <a href="{{ route('recipes.index', ['tab' => 'discovery']) }}" 
       class="pb-2 text-4xl font-bold transition-all {{ $currentTab == 'discovery' ? 'text-black border-b-4 border-green-500' : 'text-gray-400 hover:text-gray-600' }}">
        Discovery
    </a>
    <a href="{{ route('recipes.index', ['tab' => 'favorites']) }}" 
       class="pb-2 text-4xl font-bold transition-all {{ $currentTab == 'favorites' ? 'text-black border-b-4 border-green-500' : 'text-gray-400 hover:text-gray-600' }}">
        Favorites
    </a>
</div>

{{-- 3. BIG Search & Filters (Only on Discovery) --}}
@if($currentTab == 'discovery')
<div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
    <form action="{{ route('recipes.index') }}" method="GET" class="relative w-full md:w-96">
        <input type="hidden" name="tab" value="discovery">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search recipes..." 
               class="w-full pl-12 pr-6 py-4 rounded-2xl border border-gray-300 focus:outline-none focus:border-green-500 text-lg shadow-sm">
        <svg class="w-6 h-6 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </form>

    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide items-center">
        <a href="{{ route('recipes.index', ['tab' => 'discovery']) }}" 
           class="px-6 py-3 rounded-full text-base font-bold whitespace-nowrap shadow-sm transition-transform hover:scale-105 {{ !request('category') ? 'bg-green-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">All</a>
        @foreach(['Breakfast', 'Chicken', 'Beef', 'Seafood', 'Vegetarian'] as $cat)
            <a href="{{ route('recipes.index', ['tab' => 'discovery', 'category' => $cat]) }}" 
               class="px-6 py-3 rounded-full text-base font-semibold whitespace-nowrap shadow-sm transition-transform hover:scale-105 {{ request('category') == $cat ? 'bg-green-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $cat }}</a>
        @endforeach
    </div>
</div>
@endif

{{-- 4. Recipe Grid (Link Fixed Here) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
    @forelse($recipes as $recipe)
    {{-- FIXED: Added route('recipe.show') back --}}
    <a href="{{ route('recipe.show', ['id' => $recipe['idMeal']]) }}" 
       class="group relative h-80 bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all hover:-translate-y-1">
        
        <img src="{{ $recipe['strMealThumb'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        
        <div class="absolute bottom-0 left-0 w-full p-6 text-white">
            <span class="text-xs font-bold bg-green-500 px-3 py-1.5 rounded-lg uppercase tracking-wider shadow-sm">
                {{ $recipe['strCategory'] ?? 'Recipe' }}
            </span>
            <h3 class="text-2xl font-bold mt-3 leading-tight drop-shadow-md">{{ $recipe['strMeal'] }}</h3>
        </div>

        <button type="button" 
                onclick="toggleFavorite(this, event)"
                data-id="{{ $recipe['idMeal'] }}"
                data-title="{{ $recipe['strMeal'] }}"
                data-image="{{ $recipe['strMealThumb'] }}"
                data-category="{{ $recipe['strCategory'] ?? '' }}"
                class="absolute top-4 right-4 w-12 h-12 rounded-full flex items-center justify-center transition-all z-20 shadow-lg
                       {{ in_array($recipe['idMeal'], $favoriteIds ?? []) ? 'bg-white text-red-500' : 'bg-white/20 backdrop-blur-md text-white hover:bg-white hover:text-red-500' }}">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </button>
    </a>
    @empty
    <div class="col-span-full py-20 text-center">
        <div class="text-6xl mb-4">🍳</div>
        <p class="text-2xl text-gray-500 font-medium">No recipes found here.</p>
    </div>
    @endforelse
</div>

<script>
    async function toggleFavorite(btn, e) {
        e.preventDefault(); e.stopPropagation();
        const { id, title, image, category } = btn.dataset;

        try {
            const response = await fetch("{{ route('recipe.toggle') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ recipe_id: id, title, image, category })
            });
            const data = await response.json();

            if (data.status === 'added') {
                btn.classList.remove('bg-white/20', 'text-white');
                btn.classList.add('bg-white', 'text-red-500');
            } else {
                btn.classList.add('bg-white/20', 'text-white');
                btn.classList.remove('bg-white', 'text-red-500');
                if(new URLSearchParams(window.location.search).get('tab') === 'favorites') {
                    btn.closest('a').remove();
                    if(document.querySelectorAll('a.group').length === 0) location.reload();
                }
            }
        } catch (error) { console.error(error); }
    }
</script>

@endsection