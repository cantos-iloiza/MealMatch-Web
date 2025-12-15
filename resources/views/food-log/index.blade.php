{{-- resources/views/food-log/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log Food - MealMatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore-compat.js"></script>
</head>
<body class="bg-[#FFF9E6]">
    {{-- Calorie Warning Modal --}}
    <div id="calorieWarningModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-[#FFF9E6] rounded-3xl border-2 border-orange-500 max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="bg-orange-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-orange-500">Over Your Goal!</h3>
            </div>

            <p class="text-gray-800 font-medium mb-4">You have gone over your calorie goal for today.</p>

            <div class="bg-white rounded-xl border border-orange-200 p-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">Goal:</span>
                    <span class="bg-green-100 text-green-600 font-bold px-3 py-1 rounded-lg">{{ $user->calorie_goal }} cal</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">Consumed:</span>
                    <span class="bg-red-50 text-red-500 font-bold px-3 py-1 rounded-lg">{{ $todayCalories }} cal</span>
                </div>
                <div class="border-t border-gray-300 my-2"></div>
                <div class="flex justify-between items-center">
                    <span class="font-bold">Over by:</span>
                    <span class="bg-red-500 text-white font-bold px-3 py-2 rounded-lg">+{{ $caloriesOver }} cal</span>
                </div>
            </div>

            <p class="text-gray-600 text-sm text-center mb-4">Are you sure you want to continue adding food?</p>

            <div class="flex gap-3">
                <button onclick="closeWarningModal(); window.history.back();" 
                        class="flex-1 py-3 border-2 border-gray-400 rounded-xl text-gray-700 font-bold hover:bg-gray-50">
                    Go Back
                </button>
                <button onclick="closeWarningModal()" 
                        class="flex-1 py-3 bg-orange-500 text-white rounded-xl font-bold hover:bg-orange-600">
                    Continue
                </button>
            </div>
        </div>
    </div>

    {{-- Meal Selection Modal --}}
    <div id="mealSelectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
        <div class="bg-[#FFF9E6] rounded-t-3xl border-2 border-orange-500 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-[#FFF9E6] border-b border-orange-500 p-4">
                <div class="w-10 h-1 bg-orange-300 rounded-full mx-auto mb-3"></div>
                <h3 class="text-xl font-bold text-orange-800 text-center">Select a Meal</h3>
            </div>
            
            <div class="p-4 space-y-3">
                @foreach(['Breakfast' => 'wb_sunny_outlined', 'Lunch' => 'restaurant_outlined', 'Dinner' => 'dinner_dining_outlined', 'Snacks' => 'cookie_outlined'] as $meal => $icon)
                <button onclick="selectMeal('{{ $meal }}')" 
                        class="w-full flex items-center bg-white rounded-2xl border border-orange-200 p-4 hover:bg-orange-50 hover:border-orange-500 transition meal-option @if($selectedMeal === $meal) border-2 border-orange-500 bg-orange-50 @endif">
                    <div class="bg-orange-100 rounded-full p-3 mr-4">
                        <svg class="w-6 h-6 text-orange-700" fill="currentColor" viewBox="0 0 24 24">
                            @if($meal === 'Breakfast')
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            @elseif($meal === 'Lunch')
                            <path d="M16 6v8h3v8h2V2c-2.76 0-5 2.24-5 4zm-5 3H9v11h2V9zm-4 0H5V3H3v6h2v11h2V9z"/>
                            @elseif($meal === 'Dinner')
                            <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                            @else
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            @endif
                        </svg>
                    </div>
                    <span class="flex-1 text-lg font-semibold text-gray-800">{{ $meal }}</span>
                    @if($selectedMeal === $meal)
                    <svg class="w-6 h-6 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @endif
                </button>
                @endforeach
            </div>
            <div class="h-5"></div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        {{-- Header --}}
        <header class="bg-[#FFF9E6] shadow-sm border-b border-gray-200 p-4">
            <button onclick="showMealSelectModal()" 
                    class="mx-auto flex items-center justify-center gap-2 text-orange-500 font-semibold text-lg">
                <span>{{ $selectedMeal ?? 'Select a Meal' }}</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </header>

        {{-- Tabs --}}
        <div class="bg-[#FFF9E6] flex border-b border-gray-200">
            <button onclick="switchTab('all')" 
                    class="flex-1 py-3 font-semibold tab-button active-tab" 
                    data-tab="all">
                All
            </button>
            <button onclick="switchTab('favorites')" 
                    class="flex-1 py-3 font-semibold tab-button" 
                    data-tab="favorites">
                Favorites
            </button>
            <button onclick="switchTab('my-recipes')" 
                    class="flex-1 py-3 font-semibold tab-button" 
                    data-tab="my-recipes">
                My Recipes
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="p-4">
            {{-- All Tab --}}
            <div id="allTab" class="tab-content">
                {{-- Search Bar --}}
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Search 380,000+ foods (press Enter)"
                               class="w-full px-4 py-3 pl-12 pr-12 rounded-full border-2 border-orange-500 bg-white focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <div id="searchLoader" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-orange-500"></div>
                        </div>
                        <button id="clearSearch" 
                                class="hidden absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Search Results --}}
                <div id="searchResults" class="space-y-2 mb-6"></div>

                {{-- Recent Foods --}}
                @if($recentFoods->isNotEmpty())
                <div>
                    <h3 class="text-lg font-bold px-5 py-2">Recently Logged</h3>
                    <div id="recentFoods" class="space-y-2">
                        @foreach($recentFoods as $food)
                        <div class="bg-white rounded-xl mx-4 p-4 flex items-center justify-between food-item" 
                             data-food='@json([
                                "name" => $food->food_name,
                                "brand" => $food->brand,
                                "calories" => $food->calories,
                                "carbs" => $food->carbs,
                                "protein" => $food->proteins,
                                "fat" => $food->fats,
                                "servingsize" => $food->serving,
                                "isVerified" => $food->is_verified,
                                "source" => $food->source
                             ])'>
                            <div class="flex-1 cursor-pointer" onclick="viewFoodDetails(this)">
                                <h4 class="font-semibold text-base">{{ $food->food_name }}</h4>
                                <p class="text-gray-600 text-sm">
                                    {{ round($food->calories) }} cal{{ $food->brand ? ', ' . $food->brand : '' }}, {{ $food->serving }}
                                </p>
                            </div>
                            <button onclick="quickAddFood(this)" 
                                    class="bg-orange-500 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-orange-600 add-button">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-gray-600 text-base">Search for foods to get started</p>
                </div>
                @endif
            </div>

            {{-- Favorites Tab --}}
            <div id="favoritesTab" class="tab-content hidden">
                <div id="favoritesContent" class="space-y-2"></div>
                <div id="favoritesEmpty" class="hidden text-center py-16">
                    <svg class="w-20 h-20 mx-auto text-orange-300 mb-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">No Favorites Yet</h3>
                    <p class="text-gray-600 mb-8">Start adding your favorite recipes<br>to see them here!</p>
                    <button onclick="window.location.href='/recipes'" 
                            class="bg-orange-500 text-white px-8 py-4 rounded-full font-bold hover:bg-orange-600 inline-flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                        Discover Recipes
                    </button>
                </div>
            </div>

            {{-- My Recipes Tab --}}
            <div id="myRecipesTab" class="tab-content hidden">
                <div id="myRecipesContent" class="space-y-2"></div>
                <div id="myRecipesEmpty" class="hidden text-center py-16">
                    <svg class="w-20 h-20 mx-auto text-orange-300 mb-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Inay's Pansit isn't in the database (yet)</h3>
                    <p class="text-gray-600 mb-8">Create and save your own custom recipes</p>
                    <button onclick="window.location.href='/upload'" 
                            class="bg-orange-500 text-white px-8 py-4 rounded-full font-bold hover:bg-orange-600 inline-flex items-center gap-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                        Create Recipe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Firebase Config (Replace with your config)
        const firebaseConfig = {
            apiKey: "AIzaSyD0jeAW3z1842r3-44MYmpkPUrZDnvhuvI",
            authDomain: "mealmatch-web.firebaseapp.com",
            projectId: "mealmatch-web",
            storageBucket: "mealmatch-web.firebasestorage.app",
            messagingSenderId: "360288389804",
            appId: "1:360288389804:web:03355df9920be35dac2d09"
        };

        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const db = firebase.firestore();

        let currentUser = null;
        let selectedMeal = '{{ $selectedMeal ?? "" }}';
        let searchTimeout;

        // Check authentication
        auth.onAuthStateChanged((user) => {
            if (user) {
                currentUser = user;
                user.getIdToken().then(token => {
                    fetch('/api/set-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ token })
                    });
                });
            } else {
                window.location.href = '/login';
            }
        });

        // Show calorie warning if over goal
        @if($isOverGoal && !session('warning_shown'))
        window.addEventListener('load', () => {
            document.getElementById('calorieWarningModal').classList.remove('hidden');
        });
        @php session(['warning_shown' => true]); @endphp
        @endif

        function closeWarningModal() {
            document.getElementById('calorieWarningModal').classList.add('hidden');
        }

        function showMealSelectModal() {
            document.getElementById('mealSelectModal').classList.remove('hidden');
        }

        function selectMeal(meal) {
            selectedMeal = meal;
            
            fetch('{{ route("food-log.select-meal") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ meal })
            }).then(() => {
                document.getElementById('mealSelectModal').classList.add('hidden');
                location.reload();
            });
        }

        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active-tab');
                if (btn.dataset.tab === tab) {
                    btn.classList.add('active-tab');
                }
            });

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            if (tab === 'all') {
                document.getElementById('allTab').classList.remove('hidden');
            } else if (tab === 'favorites') {
                document.getElementById('favoritesTab').classList.remove('hidden');
                loadFavorites();
            } else if (tab === 'my-recipes') {
                document.getElementById('myRecipesTab').classList.remove('hidden');
                loadMyRecipes();
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchFood(e.target.value);
            }
        });

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const clearBtn = document.getElementById('clearSearch');
            if (e.target.value) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        });

        document.getElementById('clearSearch').addEventListener('click', () => {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
            document.getElementById('clearSearch').classList.add('hidden');
        });

        function searchFood(query) {
            if (!query.trim()) return;

            document.getElementById('searchLoader').classList.remove('hidden');
            document.getElementById('searchResults').innerHTML = '';

            fetch('{{ route("food-log.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ query })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('searchLoader').classList.add('hidden');
                
                if (data.results && data.results.length > 0) {
                    displaySearchResults(data.results);
                } else {
                    document.getElementById('searchResults').innerHTML = `
                        <p class="text-center text-gray-600 py-8">${data.message || 'No results found'}</p>
                    `;
                }
            })
            .catch(err => {
                document.getElementById('searchLoader').classList.add('hidden');
                console.error('Search error:', err);
            });
        }

        function displaySearchResults(results) {
            const container = document.getElementById('searchResults');
            container.innerHTML = '<h3 class="text-lg font-bold px-5 py-2">Search Results</h3>';

            results.forEach(food => {
                const serving = `${food.servingsamount} ${food.servingsize}`;
                const foodData = {
                    name: food.name,
                    brand: food.brand || '',
                    calories: food.calories,
                    carbs: food.carbs,
                    protein: food.protein,
                    fat: food.fat,
                    servingsize: serving,
                    isVerified: food.source === 'USDA' || food.source === 'OpenFoodFacts',
                    source: food.source
                };

                container.innerHTML += `
                    <div class="bg-white rounded-xl mx-4 p-4 flex items-center justify-between food-item" data-food='${JSON.stringify(foodData)}'>
                        <div class="flex-1 cursor-pointer" onclick="viewFoodDetails(this)">
                            <h4 class="font-semibold text-base">${food.name}</h4>
                            <p class="text-gray-600 text-sm">
                                ${Math.round(food.calories)} cal${food.brand ? ', ' + food.brand : ''}, ${serving}
                            </p>
                        </div>
                        <button onclick="quickAddFood(this)" 
                                class="bg-orange-500 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-orange-600 add-button">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                `;
            });
        }

        function viewFoodDetails(element) {
            const foodData = JSON.parse(element.closest('.food-item').dataset.food);
            
            fetch('{{ route("modify-food.set-item") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(foodData)
            }).then(() => {
                window.location.href = '{{ route("modify-food.show") }}';
            });
        }

        function quickAddFood(button) {
            if (!selectedMeal) {
                alert('Please select a meal category first');
                return;
            }

            const foodData = JSON.parse(button.closest('.food-item').dataset.food);
            
            const mealData = {
                category: selectedMeal,
                food_name: foodData.name,
                brand: foodData.brand,
                calories: foodData.calories,
                carbs: foodData.carbs,
                fats: foodData.fat,
                proteins: foodData.protein,
                serving: foodData.servingsize,
                is_verified: foodData.isVerified,
                source: foodData.source
            };

            fetch('{{ route("food-log.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(mealData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Show success feedback
                    button.innerHTML = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>`;
                    button.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                    button.classList.add('bg-green-500');
                    setTimeout(() => {
                        button.innerHTML = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>`;
                        button.classList.add('bg-orange-500', 'hover:bg-orange-600');
                        button.classList.remove('bg-green-500');
                    }, 2000);
                } else {
                    alert('Error adding food: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Add food error:', err);
            });
        }
        function loadFavorites() {
            const container = document.getElementById('favoritesContent');
            const emptyMessage = document.getElementById('favoritesEmpty');
            container.innerHTML = '';
            emptyMessage.classList.add('hidden');

            fetch('{{ route("food-log.favorites") }}')
            .then(res => res.json())
            .then(data => {
                if (data.favorites && data.favorites.length > 0) {
                    data.favorites.forEach(food => {
                        const serving = `${food.servingsamount} ${food.servingsize}`;
                        const foodData = {
                            name: food.name,
                            brand: food.brand || '',
                            calories: food.calories,
                            carbs: food.carbs,
                            protein: food.protein,
                            fat: food.fat,
                            servingsize: serving,
                            isVerified: food.source === 'USDA' || food.source === 'OpenFoodFacts',
                            source: food.source
                        };

                        container.innerHTML += `
                            <div class="bg-white rounded-xl mx-4 p-4 flex items-center justify-between food-item" data-food='${JSON.stringify(foodData)}'>
                                <div class="flex-1 cursor-pointer" onclick="viewFoodDetails(this)">
                                    <h4 class="font-semibold text-base">${food.name}</h4>
                                    <p class="text-gray-600 text-sm">
                                        ${Math.round(food.calories)} cal${food.brand ? ', ' + food.brand : ''}, ${serving}
                                    </p>
                                </div>
                                <button onclick="quickAddFood(this)" 
                                        class="bg-orange-500 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-orange-600 add-button">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    emptyMessage.classList
                        .remove('hidden');
                }
            })
            .catch(err => {
                console.error('Load favorites error:', err);
            });
        }
        function loadMyRecipes() {
            const container = document.getElementById('myRecipesContent');
            const emptyMessage = document.getElementById('myRecipesEmpty');
            container.innerHTML = '';
            emptyMessage.classList.add('hidden');

            fetch('{{ route("food-log.my-recipes") }}')
            .then(res => res.json())
            .then(data => {
                if (data.recipes && data.recipes.length > 0) {
                    data.recipes.forEach(food => {
                        const serving = `${food.servingsamount} ${food.servingsize}`;
                        const foodData = {
                            name: food.name,
                            brand: food.brand || '',
                            calories: food.calories,
                            carbs: food.carbs,
                            protein: food.protein,
                            fat: food.fat,
                            servingsize: serving,
                            isVerified: food.source === 'USDA' || food.source === 'OpenFoodFacts',
                            source: food.source
                        };

                        container.innerHTML += `
                            <div class="bg-white rounded-xl mx-4 p-4 flex items-center justify-between food-item" data-food='${JSON.stringify(foodData)}'>
                                <div class="flex-1 cursor-pointer" onclick="viewFoodDetails(this)">
                                    <h4 class="font-semibold text-base">${food.name}</h4>
                                    <p class="text-gray-600 text-sm">
                                        ${Math.round(food.calories)} cal${food.brand ? ', ' + food.brand : ''}, ${serving}
                                    </p>
                                </div>
                                <button onclick="quickAddFood(this)" 
                                        class="bg-orange-500 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-orange-600 add-button">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    emptyMessage.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error('Load my recipes error:', err);
            });
        }
    </script>
</body>
</html>

    {{-- Calorie Warning Modal --}}
    <div id="calorieWarningModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-3xl border-2 border-orange-500 w-full max-w-md p-6 mx-4">
            <div class="flex flex-col items-center mb-4">
                <div class="bg-orange-100 rounded-full p-4 mb-3">
                    <svg class="w-10 h-10 text-orange-500" fill="currentColor" viewBox="0 0 20 20"></svg>
                    