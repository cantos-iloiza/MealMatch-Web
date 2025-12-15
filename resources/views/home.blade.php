@extends('layouts.app')

@section('title', 'Dashboard - MealMatch')

@section('page-title')
    <h1 class="text-5xl font-bold">
        <span class="text-green-600">Dash</span><span class="text-orange-500">board</span>
    </h1>
@endsection

@section('content')
{{-- Today's Date --}}
<h2 class="text-3xl font-bold text-gray-900 mb-6">
    Today, {{ now()->format('M d') }}
</h2>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
    {{-- Daily Calories Card (Takes up 2 columns) --}}
    <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm p-8 flex flex-col justify-center relative overflow-hidden">
        <div class="flex justify-between items-center z-10">
            <div>
                <h3 class="text-4xl font-bold text-gray-900 mb-2">Daily Calories</h3>
                {{-- Dynamic Goal Text --}}
                <div class="mt-8 space-y-6">
                    {{-- Goal --}}
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🔥</span>
                        <div>
                            <p class="text-sm text-gray-400 font-medium">Calorie Goal</p>
                            <p class="text-3xl font-bold text-orange-500">{{ number_format($userGoalCalories ?? 2000) }}</p>
                        </div>
                    </div>
                    {{-- Intake --}}
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🍎</span>
                        <div>
                            <p class="text-sm text-gray-400 font-medium">Calorie Intake</p>
                            <p class="text-3xl font-bold text-red-500">{{ number_format($consumedCalories ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Circular Progress --}}
            <div class="relative">
                @php
                    $goal = $userGoalCalories ?? 2000;
                    $consumed = $consumedCalories ?? 0;
                    $remaining = $goal - $consumed;
                    // Prevent division by zero and cap at 100%
                    $percentage = $goal > 0 ? min(($consumed / $goal) * 100, 100) : 0;
                    $circumference = 2 * 3.14159 * 80; // r=80
                    $dashoffset = $circumference - ($percentage / 100) * $circumference;
                @endphp
                
                <div class="relative w-56 h-56 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        {{-- Background Circle --}}
                        <circle cx="50%" cy="50%" r="80" stroke="#f3f4f6" stroke-width="20" fill="transparent" />
                        {{-- Progress Circle --}}
                        <circle cx="50%" cy="50%" r="80" stroke="#f97316" stroke-width="20" fill="transparent" 
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="{{ $dashoffset }}" 
                                stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-5xl font-bold text-gray-800">{{ $remaining }}</span>
                        <span class="text-gray-500 text-sm font-medium mt-1">Remaining</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Side Action Cards (1 Column) --}}
    <div class="space-y-6">
        {{-- What Can I Cook Card --}}
        <a href="{{ route('whatcanicook') }}" 
           class="block bg-white rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-all border-2 border-transparent hover:border-green-200 h-40 flex flex-col justify-center">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-2xl font-bold text-gray-900">What to Cook?</h4>
                    <p class="text-gray-500 text-sm mt-1">Find recipes for your pantry</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-2xl">
                    🥣
                </div>
            </div>
        </a>

        {{-- Food Log Card --}}
        <a href="{{ route('food-log.index') }}" 
           class="block bg-white rounded-[2rem] shadow-sm p-6 hover:shadow-md transition-all border-2 border-transparent hover:border-orange-200 h-40 flex flex-col justify-center">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-2xl font-bold text-gray-900">Food Log</h4>
                    <p class="text-gray-500 text-sm mt-1">Eat, log, track, repeat</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-2xl">
                    🍽️
                </div>
            </div>
        </a>
        
        {{-- "All Recipes" hidden to match screenshot, uncomment if needed --}}
        {{-- 
        <a href="{{ route('recipes.index') }}" class="...">...</a> 
        --}}
    </div>    
</div>

{{-- Cook Again Section --}}
<div class="mb-12">
    <h3 class="text-2xl font-bold text-gray-900 mb-6">Cook again</h3>
    {{-- Grid for Recipes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="cook-again-recipes">
        {{-- Loading Skeletons --}}
        <div class="bg-gray-200 h-64 rounded-3xl animate-pulse"></div>
        <div class="bg-gray-200 h-64 rounded-3xl animate-pulse"></div>
        <div class="bg-gray-200 h-64 rounded-3xl animate-pulse"></div>
        <div class="bg-gray-200 h-64 rounded-3xl animate-pulse"></div>
    </div>
</div>

{{-- Welcome Dialog (Preserved) --}}
@if(isset($showWelcomeDialog) && $showWelcomeDialog)
<div id="welcome-dialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">🎉 Welcome!</h2>
        <div class="bg-yellow-50 rounded-2xl p-6 mb-6">
            <p class="text-center font-semibold text-gray-800 mb-3">Your starting calorie goal is set!</p>
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
    fetchMealDBData();
});

// Function to fetch data from TheMealDB API
function fetchMealDBData() {
    // For "Cook Again", we'll fetch some random breakfast items to make it look populated
    // You can change 'Breakfast' to any category or use search.php?s=chicken
    fetch('https://www.themealdb.com/api/json/v1/1/filter.php?c=Breakfast')
        .then(response => response.json())
        .then(data => {
            // Get the first 4 meals
            const meals = data.meals.slice(0, 4);
            
            // We need to fetch full details for each meal to get ingredients
            // mapped to an array of promises
            const detailPromises = meals.map(meal => 
                fetch(`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${meal.idMeal}`)
                .then(res => res.json())
                .then(d => d.meals[0])
            );

            return Promise.all(detailPromises);
        })
        .then(fullMeals => {
            renderRecipes('cook-again-recipes', fullMeals);
        })
        .catch(error => {
            console.error('Error fetching from MealDB:', error);
            document.getElementById('cook-again-recipes').innerHTML = '<p class="text-red-500">Failed to load recipes.</p>';
        });
}

function renderRecipes(containerId, recipes) {
    const container = document.getElementById(containerId);
    container.innerHTML = recipes.map(recipe => createRecipeCard(recipe)).join('');
}

function createRecipeCard(recipe) {
    // MealDB Data Mapping
    const id = recipe.idMeal;
    const title = recipe.strMeal;
    const image = recipe.strMealThumb;
    const category = recipe.strCategory || 'General';
    
    // Simulate data TheMealDB doesn't provide (Rating, Time, Author)
    const rating = (Math.random() * (5.0 - 4.0) + 4.0).toFixed(1); 
    const reviews = Math.floor(Math.random() * 300) + 50;
    const time = Math.floor(Math.random() * 20) + 15; // 15-35 mins
    
    // Extract Ingredients from strIngredient1, strIngredient2...
    let ingredients = [];
    for(let i=1; i<=5; i++) {
        if(recipe[`strIngredient${i}`] && recipe[`strIngredient${i}`] !== "") {
            ingredients.push(recipe[`strIngredient${i}`]);
        }
    }
    const ingredientsList = ingredients.join(', ');

    return `
        <a href="/recipe/${id}" class="bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
            {{-- Image Section --}}
            <div class="h-48 overflow-hidden relative group">
                <img src="${image}" alt="${title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                
                {{-- Rating Badge --}}
                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full flex items-center gap-1 shadow-lg">
                    <span class="text-yellow-400">★</span>
                    <span class="font-bold text-sm">${rating}</span>
                    <span class="text-xs text-gray-300">(${reviews})</span>
                </div>
            </div>

            {{-- Content Section --}}
            <div class="p-5 flex-1 flex flex-col">
                <h4 class="font-bold text-lg text-gray-900 mb-1 leading-tight line-clamp-1" title="${title}">${title}</h4>
                <p class="text-xs text-gray-500 mb-4">by Jelly Fisher</p> {{-- Static author for UI match --}}
                
                <div class="flex items-center gap-3 text-xs text-gray-500 font-medium mb-4">
                    <div class="flex items-center gap-1">
                        <span>🕒</span> ${time} mins
                    </div>
                    <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                    <div>${category}</div>
                </div>
                
                <div class="mt-auto pt-3 border-t border-gray-50">
                    <p class="text-[11px] text-gray-400 leading-relaxed">
                        <span class="font-bold text-gray-600">Must-have:</span> 
                        ${ingredientsList}
                    </p>
                </div>
            </div>
        </a>
    `;
}
</script>
@endpush
@endsection