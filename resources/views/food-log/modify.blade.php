{{-- resources/views/food-log/modify.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Food - MealMatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#FFF9E6]">
    <div class="max-w-3xl mx-auto min-h-screen">
        {{-- Header --}}
        <header class="bg-[#FFF9E6] shadow-sm p-4 flex items-center">
            <button onclick="window.history.back()" class="mr-4">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            <h1 class="text-lg font-semibold">Add Food</h1>
        </header>

        <div class="p-5">
            {{-- Food Name and Calories --}}
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-bold text-gray-900 flex-1 mr-4" id="foodName">{{ $food['name'] ?? 'Food Item' }}</h2>
                <div class="text-2xl font-bold text-gray-900" id="displayCalories">{{ round($food['calories'] ?? 0) }} cal</div>
            </div>

            {{-- Meal Selection --}}
            <div class="bg-white rounded-lg border-b border-gray-200 p-4 mb-1 cursor-pointer" onclick="showMealPicker()">
                <div class="flex justify-between items-center">
                    <span class="text-base text-gray-800">Meal</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-orange-500" id="selectedMealDisplay">{{ $preselectedMeal }}</span>
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Number of Servings --}}
            <div class="bg-white rounded-lg border-b border-gray-200 p-4 mb-1">
                <div class="flex justify-between items-center">
                    <span class="text-base text-gray-800">Number of Servings</span>
                    <div class="flex items-center gap-4">
                        <button onclick="changeServings(-0.5)" class="text-orange-500 hover:text-orange-600">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <span class="font-bold text-orange-500 w-16 text-center text-lg" id="servingsDisplay">1.0</span>
                        <button onclick="changeServings(0.5)" class="text-orange-500 hover:text-orange-600">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Serving Size --}}
            <div class="bg-white rounded-lg border-b border-gray-200 p-4 mb-6 cursor-pointer" onclick="showServingSizePicker()">
                <div class="flex justify-between items-center">
                    <span class="text-base text-gray-800">Serving size</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-orange-500" id="servingSizeDisplay">{{ $food['servingsize'] ?? '100 g' }}</span>
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Nutrition Card --}}
            <div class="bg-white rounded-2xl p-6 mb-6">
                <h3 class="text-xl font-bold mb-6">Nutrition info</h3>

                {{-- Pie Chart --}}
                <div class="mb-6 flex justify-center">
                    <canvas id="nutritionChart" width="200" height="200"></canvas>
                </div>

                {{-- Macros Legend --}}
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-green-500 mr-3"></div>
                        <span class="flex-1 text-base">Carbohydrates</span>
                        <span class="font-bold text-base" id="carbsDisplay">{{ round($food['carbs'] ?? 0, 1) }} g</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-yellow-600 mr-3"></div>
                        <span class="flex-1 text-base">Protein</span>
                        <span class="font-bold text-base" id="proteinDisplay">{{ round($food['protein'] ?? 0, 1) }} g</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-orange-500 mr-3"></div>
                        <span class="flex-1 text-base">Fat</span>
                        <span class="font-bold text-base" id="fatDisplay">{{ round($food['fat'] ?? 0, 1) }} g</span>
                    </div>
                </div>
            </div>

            {{-- Add Button --}}
            <button onclick="addFoodToMeal()" 
                    class="w-full bg-orange-500 text-white py-4 rounded-full text-lg font-bold hover:bg-orange-600 shadow-lg">
                Add this food
            </button>
        </div>
    </div>

    {{-- Meal Selection Modal --}}
    <div id="mealPickerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center z-50">
        <div class="bg-gray-800 rounded-t-3xl w-full max-w-2xl py-5">
            <div class="space-y-1">
                @foreach(['Breakfast', 'Lunch', 'Dinner', 'Snacks'] as $meal)
                <button onclick="selectMeal('{{ $meal }}')" 
                        class="w-full text-white text-lg py-4 hover:bg-gray-700 transition">
                    {{ $meal }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Serving Size Modal --}}
    <div id="servingSizeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-md max-h-[70vh] overflow-hidden">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex justify-between items-center">
                <div class="w-8"></div>
                <h3 class="text-lg font-bold">Select Serving Size</h3>
                <button onclick="closeServingSizeModal()" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[calc(70vh-80px)]">
                @foreach(['100 g', '1 cup', '1 piece', '1 serving', '1 bowl', '1 plate', '50 g', '150 g', '200 g'] as $size)
                <button onclick="selectServingSize('{{ $size }}')" 
                        class="w-full text-center py-4 hover:bg-gray-50 border-b border-gray-100 serving-size-option">
                    {{ $size }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Initial food data from backend
        const foodData = @json($food);
        let numberOfServings = 1.0;
        let selectedMeal = '{{ $preselectedMeal }}';
        let selectedServingSize = foodData.servingsize || '100 g';

        // Base nutritional values (per serving)
        const baseCalories = parseFloat(foodData.calories) || 0;
        const baseCarbs = parseFloat(foodData.carbs) || 0;
        const baseProtein = parseFloat(foodData.protein) || 0;
        const baseFat = parseFloat(foodData.fat) || 0;

        let nutritionChart;

        // Initialize Chart
        function initChart() {
            const ctx = document.getElementById('nutritionChart').getContext('2d');
            
            nutritionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Carbs', 'Protein', 'Fat'],
                    datasets: [{
                        data: [baseCarbs, baseProtein, baseFat],
                        backgroundColor: ['#22c55e', '#ca8a04', '#f97316'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '0%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
        }

        // Update display values
        function updateDisplay() {
            const calories = (baseCalories * numberOfServings).toFixed(0);
            const carbs = (baseCarbs * numberOfServings).toFixed(1);
            const protein = (baseProtein * numberOfServings).toFixed(1);
            const fat = (baseFat * numberOfServings).toFixed(1);

            document.getElementById('displayCalories').textContent = `${calories} cal`;
            document.getElementById('carbsDisplay').textContent = `${carbs} g`;
            document.getElementById('proteinDisplay').textContent = `${protein} g`;
            document.getElementById('fatDisplay').textContent = `${fat} g`;
            document.getElementById('servingsDisplay').textContent = numberOfServings.toFixed(1);

            // Update chart
            if (nutritionChart) {
                nutritionChart.data.datasets[0].data = [
                    parseFloat(carbs),
                    parseFloat(protein),
                    parseFloat(fat)
                ];
                nutritionChart.update();
            }
        }

        function changeServings(amount) {
            numberOfServings = Math.max(0.5, numberOfServings + amount);
            updateDisplay();
        }

        function showMealPicker() {
            document.getElementById('mealPickerModal').classList.remove('hidden');
        }

        function selectMeal(meal) {
            selectedMeal = meal;
            document.getElementById('selectedMealDisplay').textContent = meal;
            document.getElementById('mealPickerModal').classList.add('hidden');
        }

        function showServingSizePicker() {
            document.getElementById('servingSizeModal').classList.remove('hidden');
        }

        function closeServingSizeModal() {
            document.getElementById('servingSizeModal').classList.add('hidden');
        }

        function selectServingSize(size) {
            selectedServingSize = size;
            document.getElementById('servingSizeDisplay').textContent = size;
            closeServingSizeModal();
        }

        // Close modals on background click
        document.querySelectorAll('#mealPickerModal, #servingSizeModal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        function addFoodToMeal() {
            if (!selectedMeal) {
                alert('Please select a meal category');
                return;
            }

            const mealData = {
                category: selectedMeal,
                food_name: foodData.name,
                brand: foodData.brand || '',
                calories: baseCalories * numberOfServings,
                carbs: baseCarbs * numberOfServings,
                fats: baseFat * numberOfServings,
                proteins: baseProtein * numberOfServings,
                serving: `${numberOfServings} x ${selectedServingSize}`,
                is_verified: foodData.isVerified || false,
                source: foodData.source || 'Local'
            };

            fetch('{{ route("modify-food.add") }}', {
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
                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50';
                    successMsg.innerHTML = `
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>${data.message}</span>
                    `;
                    document.body.appendChild(successMsg);

                    setTimeout(() => {
                        window.location.href = '{{ route("food-log.index") }}';
                    }, 1500);
                }
            })
            .catch(err => {
                console.error('Error adding food:', err);
                alert('Failed to add food. Please try again.');
            });
        }

        // Initialize on page load
        window.addEventListener('load', () => {
            initChart();
            updateDisplay();
        });
    </script>
</body>
</html>