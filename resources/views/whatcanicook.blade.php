@extends('layouts.app')

@section('title', 'What Can I Cook? - MealMatch')

{{-- We leave page-title empty because the title is inside the main card for this specific design --}}
@section('page-title')
@endsection

@section('content')

{{-- MAIN SEARCH CARD --}}
<div class="relative bg-white rounded-[2.5rem] shadow-sm p-16 mb-8 text-center">

    {{-- CENTERED TITLE & SUBTITLE --}}
    <div class="mb-10">
        <h1 class="text-5xl font-bold text-gray-900 mb-4">
            What Can I <span class="text-orange-500">Cook?</span>
        </h1>
        <p class="text-gray-500 text-lg">
            Enter your available ingredients and discover delicious recipes you can make!
        </p>
    </div>

    {{-- SEARCH BAR --}}
    <div class="max-w-4xl mx-auto">
        {{-- Input container with focus ring styling to match theme --}}
        <div class="flex items-center bg-white border border-gray-200 rounded-full px-2 py-2 shadow-sm transition-all focus-within:ring-4 focus-within:ring-orange-50 focus-within:border-orange-200">
            <input
                id="ingredientsInput"
                type="text"
                placeholder="Enter ingredients (e.g., chicken, rice, tomatoes)"
                class="flex-1 ml-6 text-lg text-gray-700 placeholder-gray-400 focus:outline-none bg-transparent"
            >
            <button
                id="searchBtn"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-full transition shadow-md focus:outline-none shrink-0">
                Search Recipes
            </button>
        </div>

        <p class="text-sm text-gray-400 mt-4 flex items-center justify-center gap-1">
            💡 Tip: Separate ingredients with commas
        </p>
    </div>

    {{-- QUICK SEARCH PILLS --}}
    <div class="mt-10">
        <p class="text-gray-600 font-medium mb-4">Quick search:</p>

        <div class="flex flex-wrap justify-center gap-3">
            @php
                $quick = [
                    ['🍗','chicken'],
                    ['🍚','rice'],
                    ['🍅','tomato'],
                    ['🍝','pasta'],
                    ['🥩','beef'],
                    ['🥔','potato'],
                    ['🧅','onion'],
                    ['🧄','garlic'],
                ];
            @endphp

            @foreach($quick as [$emoji, $item])
                <button
                    data-ingredient="{{ $item }}"
                    class="ingredient-tag bg-yellow-100 hover:bg-yellow-200 text-gray-800 px-6 py-2 rounded-full font-medium transition shadow-sm focus:outline-none flex items-center gap-2">
                    <span>{{ $emoji }}</span> {{ ucfirst($item) }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- LOADING OVERLAY --}}
    <div id="loadingState"
         class="hidden absolute inset-0 bg-white/95 rounded-[2.5rem] flex flex-col items-center justify-center z-20">
        <div class="animate-spin h-14 w-14 border-4 border-gray-200 border-t-orange-500 rounded-full"></div>
        <p class="mt-4 text-gray-500 font-medium">Checking your pantry...</p>
    </div>
</div>

{{-- RESULTS SECTION --}}
<div id="resultsSection" class="hidden mt-12 mb-20 px-4">
    {{-- Ingredients Used --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm mb-12 border border-gray-50">
        <h3 class="font-bold text-gray-900 text-2xl mb-4">Your Ingredients:</h3>
        <div id="ingredientsList" class="flex flex-wrap gap-3"></div>
    </div>

    {{-- Complete Matches --}}
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-8">
            <span class="text-orange-500 text-3xl font-bold">✓</span>
            <h2 class="text-3xl font-bold text-gray-900">Complete Matches</h2>
            <span id="completeCount" class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-sm">0</span>
        </div>
        
        <div id="completeMatchesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"></div>
        
        <div id="noCompleteMatches" class="hidden text-gray-400 italic text-lg bg-white p-10 rounded-3xl text-center shadow-sm">
            No recipes matched 100% of your ingredients. Check the partial matches below!
        </div>
    </div>
    
    {{-- Partial Matches --}}
    <div id="partialMatchesSection">
        <div class="flex items-center gap-3 mb-8">
            <span class="text-orange-500 text-3xl font-bold">~</span>
            <h2 class="text-3xl font-bold text-gray-900">Partial Matches</h2>
            <span id="partialCount" class="bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-bold shadow-sm">0</span>
        </div>
        <div id="partialMatchesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"></div>
    </div>
</div>

{{-- NO RESULTS STATE --}}
<div id="noResultsSection"
     class="hidden max-w-lg mx-auto mt-12 text-center bg-white p-12 rounded-[2rem] shadow-sm">
    <div class="text-6xl mb-4">😕</div>
    <h3 class="text-2xl font-bold text-gray-800">No recipes found</h3>
    <p class="text-gray-500 mt-2 text-lg">
        Try simpler ingredients (e.g., "egg" instead of "boiled egg") and please separate ingredients with commas.
    </p>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingredientsInput = document.getElementById('ingredientsInput');
    const searchBtn = document.getElementById('searchBtn');
    const loadingState = document.getElementById('loadingState');
    const resultsSection = document.getElementById('resultsSection');

    // Add ingredient to input on click (Quick Search Pills)
    document.querySelectorAll('.ingredient-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const ingredient = this.dataset.ingredient;
            const currentValue = ingredientsInput.value.trim();
            
            if (currentValue && !currentValue.endsWith(',')) {
                // If input has text and no trailing comma, add comma space
                ingredientsInput.value = currentValue ? (currentValue + ', ' + ingredient) : ingredient;
            } else {
                ingredientsInput.value = currentValue + ingredient;
            }
            ingredientsInput.focus();
        });
    });

    searchBtn.addEventListener('click', searchRecipes);
    ingredientsInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') searchRecipes(); });

    async function searchRecipes() {
        const ingredients = ingredientsInput.value.trim();
        if (!ingredients) {
            alert('Please enter at least one ingredient!');
            return;
        }

        loadingState.classList.remove('hidden');
        resultsSection.classList.add('hidden');
        document.getElementById('noResultsSection').classList.add('hidden');

        try {
            const ingredientArray = ingredients.split(',').map(i => i.trim()).filter(i => i);
            const allRecipes = new Map();

            // Fetch recipes for each ingredient
            const ingredientPromises = ingredientArray.map(ingredient => 
                fetch(`https://www.themealdb.com/api/json/v1/1/filter.php?i=${ingredient}`)
                    .then(res => res.json())
                    .then(data => ({ ingredient, meals: data.meals }))
            );

            const ingredientResults = await Promise.all(ingredientPromises);

            // Combine results
            for (const { ingredient, meals } of ingredientResults) {
                if (meals) {
                    for (const meal of meals) {
                        if (!allRecipes.has(meal.idMeal)) {
                            allRecipes.set(meal.idMeal, {
                                id: meal.idMeal,
                                name: meal.strMeal,
                                image: meal.strMealThumb,
                                matchedIngredients: []
                            });
                        }
                        allRecipes.get(meal.idMeal).matchedIngredients.push(ingredient);
                    }
                }
            }

            if (allRecipes.size === 0) {
                loadingState.classList.add('hidden');
                document.getElementById('noResultsSection').classList.remove('hidden');
                return;
            }

            // Fetch details for top results (Limit to 24)
            const recipeEntries = Array.from(allRecipes.entries()).slice(0, 24);
            const detailPromises = recipeEntries.map(([id, recipe]) =>
                fetch(`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.meals && data.meals[0]) {
                            const fullRecipe = data.meals[0];
                            let totalIngredients = 0;
                            // Count actual ingredients
                            for (let i = 1; i <= 20; i++) {
                                if (fullRecipe[`strIngredient${i}`] && fullRecipe[`strIngredient${i}`].trim() !== '') totalIngredients++;
                            }
                            const matchedCount = recipe.matchedIngredients.length;
                            return {
                                ...recipe,
                                category: fullRecipe.strCategory,
                                area: fullRecipe.strArea,
                                totalIngredients,
                                matchedCount,
                                matchPercentage: Math.round((matchedCount / totalIngredients) * 100),
                                missingCount: Math.max(0, totalIngredients - matchedCount)
                            };
                        }
                        return null;
                    }).catch(() => null)
            );

            const recipesWithDetails = (await Promise.all(detailPromises)).filter(r => r !== null);
            
            // Complete matches = 100% match, Partial = anything less
            const completeMatches = recipesWithDetails.filter(r => r.matchPercentage === 100);
            const partialMatches = recipesWithDetails.filter(r => r.matchPercentage < 100)
                .sort((a, b) => b.matchPercentage - a.matchPercentage); // Sort by match percentage

            loadingState.classList.add('hidden');
            displayResults(completeMatches, partialMatches);

        } catch (error) {
            loadingState.classList.add('hidden');
            console.error(error);
            alert('Something went wrong. Please try again.');
        }
    }

    function displayResults(complete, partial) {
        const resultsSection = document.getElementById('resultsSection');
        const noResultsSection = document.getElementById('noResultsSection');
        
        if (complete.length === 0 && partial.length === 0) {
            resultsSection.classList.add('hidden');
            noResultsSection.classList.remove('hidden');
            return;
        }
        
        resultsSection.classList.remove('hidden');
        noResultsSection.classList.add('hidden');
        
        // Display ingredient tags
        const ingredientsList = document.getElementById('ingredientsList');
        const ingredients = ingredientsInput.value.split(',').map(i => i.trim()).filter(i => i);
        ingredientsList.innerHTML = ingredients.map(ing => 
            `<span class="bg-orange-500 text-white px-5 py-2 rounded-full font-medium text-sm shadow-sm capitalize">${ing}</span>`
        ).join('');
        
        // Display complete matches
        document.getElementById('completeCount').textContent = complete.length;
        const completeGrid = document.getElementById('completeMatchesGrid');
        const noCompleteMsg = document.getElementById('noCompleteMatches');
        
        if (complete.length > 0) {
            completeGrid.innerHTML = complete.map(r => createRecipeCard(r, true)).join('');
            completeGrid.classList.remove('hidden');
            noCompleteMsg.classList.add('hidden');
        } else {
            completeGrid.classList.add('hidden');
            noCompleteMsg.classList.remove('hidden');
        }

        // Display partial matches
        document.getElementById('partialCount').textContent = partial.length;
        document.getElementById('partialMatchesGrid').innerHTML = partial.map(r => createRecipeCard(r, false)).join('');
        
        // Smooth scroll to results
        window.scrollTo({ top: 500, behavior: 'smooth' });
    }

    function createRecipeCard(recipe, isComplete) {
        const badgeColor = isComplete ? 'bg-green-500' : 'bg-yellow-500';
        const badgeText = isComplete ? '100% Match' : `${recipe.matchPercentage}% Match`;
        const matchColor = isComplete ? 'text-green-600' : 'text-gray-600';

        return `
            <a href="/recipe/${recipe.id}" class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group h-full flex flex-col border border-gray-100">
                <div class="h-56 relative overflow-hidden">
                    <img src="${recipe.image}" alt="${recipe.name}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <span class="${badgeColor} text-white px-3 py-1 rounded-lg text-xs font-bold absolute top-4 right-4 shadow-lg uppercase tracking-wider">
                        ${badgeText}
                    </span>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-bold text-xl text-gray-900 mb-3 leading-tight line-clamp-2">
                        ${recipe.name}
                    </h3>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-yellow-50 text-yellow-800 px-3 py-1 rounded-lg text-xs font-bold uppercase">
                            ${recipe.category}
                        </span>
                        <span class="bg-orange-50 text-orange-800 px-3 py-1 rounded-lg text-xs font-bold uppercase">
                            ${recipe.area}
                        </span>
                    </div>
                    
                    <div class="mt-auto">
                        <p class="${matchColor} font-bold text-sm mb-1">
                            ${recipe.matchedCount} / ${recipe.totalIngredients} ingredients
                        </p>
                        ${!isComplete ? `<p class="text-gray-400 text-xs font-medium mb-4">Missing ${recipe.missingCount} ingredients</p>` : '<div class="mb-4"></div>'}
                        
                        <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-2xl transition-all shadow-md hover:shadow-lg">
                            View Recipe
                        </button>
                    </div>
                </div>
            </a>
        `;
    }
});
</script>
@endpush