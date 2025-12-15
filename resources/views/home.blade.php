@extends('layouts.app')

@section('title', 'Dashboard - MealMatch')

@section('content')
{{-- Today's Date --}}
<h2 class="text-3xl font-bold text-gray-900 mb-6">
    Today, {{ now()->format('M d') }}
</h2>

<div class="grid grid-cols-3 gap-6 mb-8">
    {{-- Daily Calories Card (2 columns) --}}
    <div class="col-span-2 bg-white/70 backdrop-blur-sm rounded-3xl shadow-lg p-8">
        <div class="mb-6">
            <h3 class="text-5xl font-bold text-gray-900 mb-2">Daily Calories</h3>
            <p class="text-xl text-gray-500">Goal - Food = Remaining</p>
        </div>
        
        <div class="flex items-center justify-between">
            {{-- Calorie Stats --}}
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center">
                        <span class="text-3xl">🔥</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Calorie Goal</p>
                        <p class="text-4xl font-bold text-orange-500">{{ number_format($userGoalCalories ?? 0) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center">
                        <span class="text-3xl">🍎</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Calorie Intake</p>
                        <p class="text-4xl font-bold text-orange-500" id="consumed-calories">{{ number_format($consumedCalories ?? 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Circular Progress --}}
            <div class="relative w-64 h-64">
                @php
                    $goalCal = $userGoalCalories ?? 2000;
                    $consumedCal = $consumedCalories ?? 0;
                    $remaining = $goalCal - $consumedCal;
                    $progress = $goalCal > 0 ? ($consumedCal / $goalCal) * 100 : 0;
                    $isOver = $consumedCal > $goalCal;
                @endphp
                
                <svg class="transform -rotate-90 w-64 h-64">
                    <circle cx="128" cy="128" r="110" stroke="#e5e7eb" stroke-width="24" fill="none"></circle>
                    <circle cx="128" cy="128" r="110" 
                            stroke="{{ $isOver ? '#ef4444' : '#ff9800' }}" 
                            stroke-width="24" 
                            fill="none"
                            stroke-dasharray="{{ min($progress, 100) * 6.91 }} 691"
                            stroke-linecap="round"
                            id="calorie-progress">
                    </circle>
                </svg>
                
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-3xl font-bold {{ $isOver ? 'text-red-500' : 'text-gray-900' }}" id="remaining-calories">
                        {{ number_format(abs($remaining)) }}
                    </p>
                    <p class="text-lg {{ $isOver ? 'text-red-500' : 'text-gray-600' }} mt-2" id="remaining-label">
                        {{ $isOver ? 'Over' : 'Remaining' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

{{-- Right Side Cards --}}
    <div class="space-y-6">
        {{-- What Can I Cook Card --}}
        <a href="{{ route('whatcanicook') }}" 
           class="block bg-white/70 backdrop-blur-sm rounded-3xl shadow-lg p-6 hover:shadow-xl transition-all border-2 border-green-200 hover:border-green-300">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                    <span class="text-4xl">🍳</span>
                </div>
            </div>
            <h4 class="text-3xl font-bold text-gray-900 mb-2">What Can I Cook?</h4>
            <p class="text-xl text-gray-600">Find recipes for your pantry</p>
        </a>

        {{-- NEW: Recipes Card --}}
        {{-- Make sure to define 'recipes.index' in your web.php --}}
        <a href="{{ route('recipes.index') }}" 
           class="block bg-white/70 backdrop-blur-sm rounded-3xl shadow-lg p-6 hover:shadow-xl transition-all border-2 border-blue-200 hover:border-blue-300">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <span class="text-4xl">📖</span>
                </div>
            </div>
            <h4 class="text-3xl font-bold text-gray-900 mb-2">All Recipes</h4>
            <p class="text-xl text-gray-600">Browse our collection</p>
        </a>
        
        {{-- Food Log Card --}}
        <a href="{{ route('food-log.index') }}" 
           class="block bg-white/70 backdrop-blur-sm rounded-3xl shadow-lg p-6 hover:shadow-xl transition-all border-2 border-orange-200 hover:border-orange-300">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <span class="text-4xl">🍽️</span>
                </div>
            </div>
            <h4 class="text-3xl font-bold text-gray-900 mb-2">Food Log</h4>
            <p class="text-xl text-gray-600">Eat, log, track, repeat</p>
        </a>
    </div>    

{{-- Cook Again Section --}}
<div id="cook-again-section" class="mb-8" style="display: none;">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Cook again</h3>
    <div class="grid grid-cols-4 gap-6" id="cook-again-recipes">
        @include('partials.recipe-skeleton')
    </div>
</div>

{{-- Cook Again --}}
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Cook Again</h3>
    <div class="grid grid-cols-4 gap-6" id="suggested-recipes">
        @include('partials.recipe-skeleton')
    </div>
</div>

{{-- Try These Recipes --}}
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Try These Recipes</h3>
    <div class="grid grid-cols-4 gap-6" id="try-these-recipes">
        @include('partials.recipe-skeleton')
    </div>
</div>

{{-- High-Protein Recipes --}}
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Discover High-Protein Recipes</h3>
    <div class="grid grid-cols-4 gap-6" id="protein-recipes">
        @include('partials.recipe-skeleton')
    </div>
</div>

{{-- Welcome Dialog --}}
@if(isset($showWelcomeDialog) && $showWelcomeDialog)
<div id="welcome-dialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">🎉 Welcome!</h2>
        
        <div class="bg-yellow-50 rounded-2xl p-6 mb-6">
            <p class="text-center font-semibold text-gray-800 mb-3">
                Your starting calorie goal is now set! You can adjust it anytime in Settings.
            </p>
            <p class="text-center text-sm text-gray-600">
                If you prefer not to track calories, feel free to ignore it.
            </p>
        </div>
        
        <button onclick="document.getElementById('welcome-dialog').remove()" 
                class="w-full bg-green-500 text-white font-semibold py-4 rounded-2xl hover:bg-green-600 transition text-lg">
            Got it!
        </button>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRecipes();
});

function loadRecipes() {
    fetch('{{ route("load-recipes") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.hasCookedRecipes || data.hasLoggedMeals) {
                    document.getElementById('cook-again-section').style.display = 'block';
                    renderRecipes('cook-again-recipes', data.cookAgainRecipes);
                }
                
                renderRecipes('Suggested-Recipes', data.suggestedRecipes);
                renderRecipes('try-these-recipes', data.tryTheseRecipes);
                renderRecipes('protein-recipes', data.proteinRecipes);
            }
        })
        .catch(error => {
            console.error('Error loading recipes:', error);
        });
}

function renderRecipes(containerId, recipes) {
    const container = document.getElementById(containerId);
    
    if (recipes.length === 0) {
        container.innerHTML = '<p class="col-span-4 text-gray-500 text-center py-8">No recipes available</p>';
        return;
    }
    
    container.innerHTML = recipes.map(recipe => createRecipeCard(recipe)).join('');
}

function createRecipeCard(recipe) {
    const title = recipe.title || recipe.name || 'Recipe';
    const author = recipe.author || recipe.userName || 'Unknown';
    const image = recipe.image || recipe.strMealThumb || '';
    const cookTime = recipe.readyInMinutes || recipe.prep_time || 0;
    const rating = recipe.averageRating || 4.3;
    const totalRatings = recipe.totalRatings || 237;
    const ingredients = recipe.ingredients || [];
    const category = recipe.category || recipe.strCategory || 'Vegetarian';
    
    return `
        <a href="/recipe/${recipe.id}" class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-lg hover:shadow-xl transition-all overflow-hidden border-2 border-transparent hover:border-green-200">
            <div class="h-48 overflow-hidden bg-gray-200 relative">
                ${image ? `<img src="${image}" alt="${title}" class="w-full h-full object-cover">` : 
                    '<div class="w-full h-full flex items-center justify-center"><span class="text-6xl">🍽️</span></div>'}
                <div class="absolute top-3 right-3 bg-black/70 text-white px-3 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <span class="font-semibold">${rating.toFixed(1)}</span>
                    <span class="text-xs">(${totalRatings})</span>
                </div>
            </div>
            <div class="p-5">
                <h4 class="font-bold text-lg text-gray-900 mb-2 truncate">${title}</h4>
                <p class="text-sm text-gray-600 mb-3">by ${author}</p>
                
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                    <span class="text-sm text-gray-600">${cookTime > 0 ? cookTime + ' mins' : '20-25 mins'} · ${category}</span>
                </div>
                
                ${ingredients.length > 0 ? `
                    <p class="text-xs text-gray-500">
                        Must-have ingredients: <span class="font-semibold text-gray-700">${ingredients.slice(0, 3).join(', ')}</span>
                    </p>
                ` : ''}
            </div>
        </a>
    `;
}
</script>
@endpush
@endsection