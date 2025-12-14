<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Log History - MealMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/profile.js'])
    
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
        
        .day-card {
            transition: all 0.3s ease;
        }
        
        .day-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .filter-btn {
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            transform: scale(1.05);
        }
        
        .calendar-modal {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .meal-detail-card {
            transition: all 0.3s ease;
        }
        
        .meal-detail-card:hover {
            transform: translateX(4px);
        }

        /* New Meal Card Styles matching the image */
        .meal-card {
            border-radius: 1.5rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .meal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .meal-card.breakfast-card {
            background-color: #fef9f0;
        }

        .meal-card.lunch-card {
            background-color: #fff5f0;
        }

        .meal-card.dinner-card {
            background-color: #f0f8f5;
        }

        .meal-card.snacks-card {
            background-color: #fff3f0;
        }

        .meal-icon-circle {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .meal-icon-circle i {
            font-size: 1.75rem;
        }

        .meal-icon-circle.breakfast-icon i {
            color: #f7941d;
        }

        .meal-icon-circle.lunch-icon i {
            color: #f7941d;
        }

        .meal-icon-circle.dinner-icon i {
            color: #6b9080;
        }

        .meal-icon-circle.snacks-icon i {
            color: #f7941d;
        }

        .meal-add-button {
            height: 2.5rem;
            padding: 0 1rem;
            border-radius: 0.75rem;
            background-color: #D4EBD5;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .meal-add-button:hover {
            background-color: #B8DDB9; /* Darker shade of #D4EBD5 */
            transform: scale(1.05);
        }

        .meal-add-button i {
            font-size: 1.5rem;
            color: #2d2d2d; /* Changed to dark gray/black */
        }

        .meal-add-button span {
            color: #2d2d2d; /* Add this for the text color */
            font-weight: 500; /* Optional: make it medium weight */
        }

        .food-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .food-item-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .food-item-row:first-child {
            padding-top: 0;
        }

        .food-item-name {
            font-size: 1rem;
            color: #2d2d2d;
            font-weight: 400;
        }

        .food-item-calories {
            font-size: 1rem;
            color: #888;
            font-weight: 400;
        }
    </style>
</head>
<body class="bg-[#FFF5CF] min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-[#f7941d] to-[#f5b461] text-white px-6 py-5 shadow-lg rounded-b-[2rem]">
        <div class="max-w-[1400px] mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold mb-1">Profile & Log History</h1>
                <p class="text-white/90 text-sm">Track your nutrition journey</p>
            </div>
            <button onclick="window.location.href='/'" class="text-white hover:text-white/80 text-2xl transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-[1400px] mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-6">
            
            <!-- LEFT SIDEBAR - Profile & Stats -->
            <div class="space-y-5">
                <!-- Profile Card -->
                <div class="bg-gradient-to-br from-[#ffffff] to-[#ffffff] rounded-[1.5rem] p-6 shadow-lg text-black">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-full bg-gray/20 border-4 border-orange flex items-center justify-center overflow-hidden">
                                <span class="text-3xl font-bold" id="profileInitials">JC</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold mb-1" id="userName">Juan Dela Cruz</h2>
                            <p class="text-black/90 text-sm" id="userEmail">juandelacruz@email.com</p>
                        </div>
                        <button class="text-black/80 hover:text-black text-xl transition-all">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>

                <!-- Weekly Login Streak -->
                <div class="bg-[#ffffff] rounded-[1.5rem] p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-5">
                        <i class="fas fa-fire text-[#f7941d] text-2xl"></i>
                        <h3 class="text-lg font-bold text-gray-800">Weekly Streak</h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-4" id="streakText">5 days logged</p>
                    <div class="flex justify-between gap-2">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-[#f7941d] text-white flex items-center justify-center font-bold text-sm day-circle" data-day="1">1</div>
                            <span class="text-xs text-gray-600 font-medium">S</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-[#f7941d] text-white flex items-center justify-center font-bold text-sm day-circle" data-day="2">2</div>
                            <span class="text-xs text-gray-600 font-medium">M</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-[#f7941d] text-white flex items-center justify-center font-bold text-sm day-circle" data-day="3">3</div>
                            <span class="text-xs text-gray-600 font-medium">T</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-[#f7941d] text-white flex items-center justify-center font-bold text-sm day-circle" data-day="4">4</div>
                            <span class="text-xs text-gray-600 font-medium">W</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-[#f7941d] text-white flex items-center justify-center font-bold text-sm day-circle" data-day="5">5</div>
                            <span class="text-xs text-gray-600 font-medium">T</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-white/50 text-gray-400 flex items-center justify-center font-bold text-sm day-circle" data-day="6">6</div>
                            <span class="text-xs text-gray-600 font-medium">F</span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-10 rounded-full bg-white/50 text-gray-400 flex items-center justify-center font-bold text-sm day-circle" data-day="7">7</div>
                            <span class="text-xs text-gray-600 font-medium">S</span>
                        </div>
                    </div>
                </div>

                <!-- Highest Streak -->
                <div class="bg-[#ffffff] rounded-[1.5rem] p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-chart-line text-[#ff9800] text-xl"></i>
                        <h3 class="text-lg font-bold text-gray-800">Highest Streak</h3>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-5xl font-bold text-[#4caf50]" id="highestStreak">21</p>
                        <p class="text-gray-700 font-medium text-lg">days</p>
                    </div>
                </div>

                <!-- Average Calories -->
                <div class="bg-[#ffffff] rounded-[1.5rem] p-6 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-fire text-[#ff9800] text-xl"></i>
                        <h3 class="text-lg font-bold text-gray-800">Avg Daily Intake</h3>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-5xl font-bold text-[#4caf50]" id="avgCalories">1850</p>
                        <p class="text-gray-700 font-medium text-lg">cal</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE - Log History -->
            <div class="space-y-5">
                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-3 items-center">
                    <span class="text-gray-700 font-semibold text-base">View:</span>
                    <div class="flex gap-3">
                        <button onclick="filterByDate('today')" class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm bg-white text-gray-700 hover:bg-[#6b9080] hover:text-white transition-all shadow-md" id="todayBtn">
                            Today
                        </button>
                        <button onclick="filterByDate('thisWeek')" class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm bg-white text-gray-700 hover:bg-[#6b9080] hover:text-white transition-all shadow-md" id="thisWeekBtn">
                            This Week
                        </button>
                        <button onclick="openCalendar()" class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm bg-white text-gray-700 hover:bg-[#6b9080] hover:text-white transition-all shadow-md flex items-center gap-2" id="customBtn">
                            <i class="fas fa-calendar text-[#f7941d]"></i> Custom Date
                        </button>
                    </div>
                </div>

                <!-- Log History Container -->
                <div class="bg-white rounded-[1.5rem] p-6 shadow-lg min-h-[700px]">
                    <!-- This content will be dynamically loaded -->
                    <div id="logContent">
                        <!-- Content loaded by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Modal -->
    <div id="calendarModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-[1.5rem] p-8 max-w-md w-11/12 shadow-2xl calendar-modal">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Custom Date Range</h3>
                <button onclick="closeCalendar()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-all">×</button>
            </div>
            
            <div class="space-y-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">From</label>
                    <input type="date" id="dateFrom" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#6b9080] focus:outline-none bg-[#fef5e7] text-gray-700 font-medium">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                    <input type="date" id="dateTo" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#6b9080] focus:outline-none bg-[#fef5e7] text-gray-700 font-medium">
                </div>
            </div>
            
            <button onclick="updateDateRange()" class="w-full bg-[#6b9080] hover:bg-[#5a8070] text-white font-bold py-3 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Apply
            </button>
        </div>
    </div>

    <!-- Expanded Day Modal (for This Week view) -->
    <div id="dayDetailModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-[1.5rem] p-8 max-w-3xl w-11/12 max-h-[85vh] overflow-y-auto shadow-2xl calendar-modal">
            <div class="flex justify-between items-center mb-6 sticky top-0 bg-white pb-4 border-b-2 border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800" id="modalDayTitle">Monday, Dec 8</h3>
                <button onclick="closeDayDetail()" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-all">×</button>
            </div>
            
            <div id="dayDetailContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</body>
</html>








