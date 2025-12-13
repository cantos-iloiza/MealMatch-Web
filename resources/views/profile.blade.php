<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Log History - MealMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite + Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/profile.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
        
        .meal-card {
            transition: all 0.3s ease;
        }
        
        .meal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .filter-btn {
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .calendar-modal {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-[#fce5cd] min-h-screen">
    <!-- Header -->
    <div class="bg-[#fda64a] text-white px-8 py-6 shadow-lg">
        <div class="max-w-[1600px] mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Profile & Log History</h1>
            <button onclick="window.location.href='/'" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-[1600px] mx-auto px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-[450px_1fr] gap-8">
            
            <!-- LEFT SIDE - Profile & Stats -->
            <div class="space-y-6">
                <!-- Profile Card -->
                <div class="bg-[#fda64a] rounded-3xl p-8 shadow-lg text-white">
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <img src="" alt="Profile" id="profilePic" class="w-24 h-24 rounded-full object-cover border-4 border-white bg-white/30">
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold mb-1" id="userName">Loading...</h2>
                            <p class="text-white/90 text-sm" id="userEmail">Loading...</p>
                        </div>
                        <button class="text-white/80 hover:text-white text-xl">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>

                <!-- Weekly Login Streak -->
                <div class="bg-[#ffcc3f] rounded-3xl p-8 shadow-lg">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-fire text-[#fb7e00] text-2xl"></i>
                        <h3 class="text-xl font-bold text-gray-800">Weekly Login Streak</h3>
                    </div>
                    <div class="flex justify-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-full bg-[#fb7e00] text-white flex items-center justify-center font-bold text-lg day-circle" data-day="1">1</div>
                        <div class="w-12 h-12 rounded-full bg-[#fb7e00] text-white flex items-center justify-center font-bold text-lg day-circle" data-day="2">2</div>
                        <div class="w-12 h-12 rounded-full bg-[#fb7e00] text-white flex items-center justify-center font-bold text-lg day-circle" data-day="3">3</div>
                        <div class="w-12 h-12 rounded-full bg-[#fb7e00] text-white flex items-center justify-center font-bold text-lg day-circle" data-day="4">4</div>
                        <div class="w-12 h-12 rounded-full bg-[#fb7e00] text-white flex items-center justify-center font-bold text-lg day-circle" data-day="5">5</div>
                        <div class="w-12 h-12 rounded-full bg-white/50 text-gray-400 flex items-center justify-center font-bold text-lg day-circle" data-day="6">6</div>
                        <div class="w-12 h-12 rounded-full bg-white/50 text-gray-400 flex items-center justify-center font-bold text-lg day-circle" data-day="7">7</div>
                    </div>
                    <p class="text-center text-gray-700 font-semibold" id="streakText">5 consecutive days this week</p>
                </div>

                <!-- Highest Streak -->
                <div class="bg-[#d1dfd2] rounded-3xl p-8 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-chart-line text-[#fb7e00] text-2xl"></i>
                        <h3 class="text-xl font-bold text-gray-800">Highest Streak</h3>
                    </div>
                    <div class="text-left">
                        <p class="text-5xl font-bold text-[#fb7e00] mb-2" id="highestStreak">21</p>
                        <p class="text-gray-700 font-medium">days</p>
                    </div>
                </div>

                <!-- Average Calories -->
                <div class="bg-[#d1dfd2] rounded-3xl p-8 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-fire text-[#fb7e00] text-2xl"></i>
                        <h3 class="text-xl font-bold text-gray-800">Average Calories Per Day</h3>
                    </div>
                    <div class="text-left">
                        <p class="text-5xl font-bold text-[#fb7e00] mb-2" id="avgCalories">1850</p>
                        <p class="text-gray-700 font-medium">calories</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE - Today's Log History -->
            <div class="space-y-6">
                <!-- Filter Buttons -->
                <div class="flex gap-4 items-center">
                    <span class="text-gray-800 font-semibold text-lg">Filter by Date</span>
                    <div class="flex gap-3 flex-1 justify-end">
                        <button onclick="filterByDate('today')" class="filter-btn px-6 py-2.5 rounded-xl font-semibold text-gray-700 bg-white hover:bg-[#fda64a] hover:text-white transition-all" id="todayBtn">
                            Today
                        </button>
                        <button onclick="filterByDate('thisWeek')" class="filter-btn px-6 py-2.5 rounded-xl font-semibold text-gray-700 bg-white hover:bg-[#fda64a] hover:text-white transition-all active" id="thisWeekBtn">
                            This Week
                        </button>
                        <button onclick="openCalendar()" class="filter-btn px-6 py-2.5 rounded-xl font-semibold bg-[#fda64a] text-white hover:bg-[#fb7e00] transition-all flex items-center gap-2" id="customBtn">
                            <i class="fas fa-calendar"></i> Custom Date
                        </button>
                    </div>
                </div>

                <!-- Log History Container -->
                <div class="bg-[#f9cb9c] rounded-3xl p-8 shadow-lg min-h-[600px]">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6" id="logTitle">Today's Log History</h2>
                    
                    <!-- Log Content -->
                    <div id="logContent">
                        <!-- Empty state - will be replaced by JS -->
                        <div class="text-center py-20" id="emptyState">
                            <i class="fas fa-utensils text-gray-400 text-7xl mb-6 opacity-40"></i>
                            <p class="text-gray-500 text-lg font-medium">No food logs yet</p>
                            <p class="text-gray-400 text-sm mt-2">Start logging your meals!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Modal -->
    <div id="calendarModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center calendar-modal">
        <div class="bg-[#d1dfd2] rounded-3xl p-8 max-w-md w-11/12 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Custom Date</h3>
                <button onclick="closeCalendar()" class="text-gray-500 hover:text-gray-700 text-3xl leading-none">×</button>
            </div>
            
            <!-- Calendar would go here - simplified for now -->
            <div class="space-y-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">From</label>
                    <input type="date" id="dateFrom" class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-[#93c47d] focus:outline-none bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                    <input type="date" id="dateTo" class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-[#93c47d] focus:outline-none bg-white">
                </div>
            </div>
            
            <button onclick="updateDateRange()" class="w-full bg-[#93c47d] hover:bg-[#79b4b0] text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Update
            </button>
        </div>
    </div>
</body>
</html>