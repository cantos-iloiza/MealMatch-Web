// resources/js/profile.js

let currentUserId = null;
let currentView = 'today'; // 'today', 'thisWeek', 'custom'
let weeklyData = []; // Store weekly data globally
let foodLogsCache = {}; // Cache for meal logs by date

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page loaded - Loading real data from Firebase');
    initializeProfile();
});

// Main initialization function
async function initializeProfile() {
    try {
        await loadRealData();
    } catch (error) {
        console.error('Error initializing profile:', error);
        showErrorMessage('Unable to load profile data. Please refresh the page.');
    }
}

// ====== REAL DATA FUNCTIONS (Firebase Backend) ======

async function loadRealData() {
    try {
        // Load all profile data in parallel
        await Promise.all([
            loadTodayView(),
            loadRealProfile(),
            loadRealStreak(),
            loadRealStats()
        ]);
    } catch (error) {
        console.error('Error loading real data:', error);
        showErrorMessage('Unable to connect to server. Please check your connection and try again.');
    }
}

async function loadRealProfile() {
    try {
        const response = await fetch('/api/profile/user-data');
        const result = await response.json();
        
        if (result.success && result.data) {
            const data = result.data;
            document.getElementById('userName').textContent = data.name || 'User';
            document.getElementById('userEmail').textContent = data.email || 'Logged in';
            
            // Handle avatar/photo URL if available
            if (data.photoURL) {
                const avatarContainer = document.getElementById('profile-avatar-container');
                avatarContainer.innerHTML = `<img src="${data.photoURL}" alt="Profile" class="w-full h-full object-cover">`;
            }
        } else {
            console.warn('Failed to load user profile:', result.message);
            // Show error in UI
            document.getElementById('userName').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i> Unable to load</span>';
            document.getElementById('userEmail').textContent = 'Please refresh the page';
        }
    } catch (error) {
        console.error('Error loading profile:', error);
        // Show error in UI
        document.getElementById('userName').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i> Unable to load</span>';
        document.getElementById('userEmail').textContent = 'Connection error';
    }
}

async function loadRealStreak() {
    try {
        const response = await fetch('/api/profile/weekly-streak');
        const result = await response.json();
        
        if (result.success && result.data) {
            const streakDays = result.data.weeklyStreak || 0;
            document.getElementById('streakText').textContent = `${streakDays} days logged`;
            
            // Update circles
            const circles = document.querySelectorAll('.day-circle');
            circles.forEach((circle, index) => {
                if (index < streakDays) {
                    circle.classList.remove('bg-white/50', 'text-gray-400');
                    circle.classList.add('bg-[#f7941d]', 'text-white');
                } else {
                    circle.classList.remove('bg-[#f7941d]', 'text-white');
                    circle.classList.add('bg-white/50', 'text-gray-400');
                }
            });
        } else {
            console.warn('Failed to load weekly streak:', result.message);
            document.getElementById('streakText').innerHTML = '<span class="text-red-500"><i class="fas fa-exclamation-triangle"></i> Unable to load streak</span>';
        }
    } catch (error) {
        console.error('Error loading streak:', error);
        document.getElementById('streakText').innerHTML = '<span class="text-red-500"><i class="fas fa-exclamation-triangle"></i> Connection error</span>';
    }
}

async function loadRealStats() {
    try {
        // Load highest streak
        const streakResponse = await fetch('/api/profile/highest-streak');
        const streakResult = await streakResponse.json();
        
        if (streakResult.success && streakResult.data) {
            document.getElementById('highestStreak').textContent = streakResult.data.highestStreak || 0;
        } else {
            console.warn('Failed to load highest streak:', streakResult.message);
            document.getElementById('highestStreak').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i></span>';
        }
        
        // Load average calories
        const caloriesResponse = await fetch('/api/profile/average-calories');
        const caloriesResult = await caloriesResponse.json();
        
        if (caloriesResult.success && caloriesResult.data) {
            document.getElementById('avgCalories').textContent = caloriesResult.data.averageCalories || 0;
        } else {
            console.warn('Failed to load average calories:', caloriesResult.message);
            document.getElementById('avgCalories').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i></span>';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        document.getElementById('highestStreak').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i></span>';
        document.getElementById('avgCalories').innerHTML = '<span class="text-sm text-red-500"><i class="fas fa-exclamation-triangle"></i></span>';
    }
}

// Helper: Calculate total calories from meal logs
function calculateTotalCalories(logs) {
    return logs.reduce((sum, log) => sum + log.calories, 0);
}

// Helper: Get logs grouped by category for a date
async function getLogsGroupedByCategory(dateStr) {
    try {
        const response = await fetch(`/api/profile/logs-by-date?date=${dateStr}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            return result.data;
        }
        
        console.warn('No logs found for date:', dateStr);
        return {
            'Breakfast': [],
            'Lunch': [],
            'Dinner': [],
            'Snacks': []
        };
    } catch (error) {
        console.error('Error fetching logs for date:', dateStr, error);
        return {
            'Breakfast': [],
            'Lunch': [],
            'Dinner': [],
            'Snacks': []
        };
    }
}

// ====== VIEW SWITCHING FUNCTIONS ======

async function loadTodayView() {
    currentView = 'today';
    updateFilterButtons('today');
    
    try {
        // Get today's date in YYYY-MM-DD format
        const today = new Date();
        const todayStr = formatDate(today);
        
        const todayData = await getLogsGroupedByCategory(todayStr);
        
        const allLogs = [...todayData.Breakfast, ...todayData.Lunch, ...todayData.Dinner, ...todayData.Snacks];
        const totalLogged = calculateTotalCalories(allLogs);
        const goal = 2000; // You can fetch this from user profile if needed
        const remaining = goal - totalLogged;
        const status = totalLogged > goal ? 'Over Goal' : 'On Track';
        
        renderTodayView({
            date: todayStr,
            dayName: today.toLocaleDateString('en-US', { weekday: 'long' }),
            dayNum: today.getDate(),
            month: today.toLocaleDateString('en-US', { month: 'short' }),
            goal: goal,
            logged: totalLogged,
            remaining: remaining,
            status: status,
            meals: todayData
        });
    } catch (error) {
        console.error('Error loading today view:', error);
        const container = document.getElementById('logContent');
        container.innerHTML = renderErrorState('Unable to load today\'s logs. Please try again.');
    }
}

async function loadThisWeekView() {
    currentView = 'thisWeek';
    updateFilterButtons('thisWeek');
    
    try {
        const container = document.getElementById('logContent');
        container.innerHTML = `
            <div class="text-center py-24">
                <div class="mb-6">
                    <i class="fas fa-spinner fa-spin text-[#6b9080] text-6xl"></i>
                </div>
                <p class="text-gray-600 text-lg">Loading weekly logs...</p>
            </div>
        `;
        
        const today = new Date();
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay()); // Get Sunday
        
        const weekDates = [];
        for (let i = 0; i < 7; i++) {
            const date = new Date(weekStart);
            date.setDate(weekStart.getDate() + i);
            weekDates.push(formatDate(date));
        }
        
        const startDate = weekDates[0];
        const endDate = weekDates[6];
        
        const response = await fetch(`/api/profile/logs-in-range?start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to load logs');
        }
        
        const logsData = result.data;
        
        weeklyData = weekDates.map(dateStr => {
            const meals = logsData[dateStr] || {
                'Breakfast': [],
                'Lunch': [],
                'Dinner': [],
                'Snacks': []
            };
            const allLogs = [...meals.Breakfast, ...meals.Lunch, ...meals.Dinner, ...meals.Snacks];
            const totalLogged = calculateTotalCalories(allLogs);
            const goal = 2000;
            const remaining = goal - totalLogged;
            
            const date = new Date(dateStr);
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            return {
                date: dateStr,
                dayName: dayNames[date.getDay()],
                dayNum: date.getDate(),
                month: months[date.getMonth()],
                goal: goal,
                logged: totalLogged,
                remaining: remaining,
                status: totalLogged === 0 ? 'No Logs' : totalLogged > goal ? 'Over Goal' : 'On Track',
                meals: meals
            };
        });
        
        renderWeeklyOverview(weeklyData);
    } catch (error) {
        console.error('Error loading week view:', error);
        const container = document.getElementById('logContent');
        container.innerHTML = renderErrorState('Unable to load weekly logs. Please check your connection and try again.');
    }
}

function updateFilterButtons(active) {
    const todayBtn = document.getElementById('todayBtn');
    const thisWeekBtn = document.getElementById('thisWeekBtn');
    const customBtn = document.getElementById('customBtn');
    
    // Reset all
    [todayBtn, thisWeekBtn, customBtn].forEach(btn => {
        btn.classList.remove('bg-[#6b9080]', 'text-white', 'active');
        btn.classList.add('bg-white', 'text-gray-700');
    });
    
    // Set active
    if (active === 'today') {
        todayBtn.classList.remove('bg-white', 'text-gray-700');
        todayBtn.classList.add('bg-[#6b9080]', 'text-white', 'active');
    } else if (active === 'thisWeek') {
        thisWeekBtn.classList.remove('bg-white', 'text-gray-700');
        thisWeekBtn.classList.add('bg-[#6b9080]', 'text-white', 'active');
    } else if (active === 'custom') {
        customBtn.classList.remove('bg-white', 'text-gray-700');
        customBtn.classList.add('bg-[#6b9080]', 'text-white', 'active');
    }
}

// ====== RENDER FUNCTIONS ======

function renderTodayView(dayData) {
    const container = document.getElementById('logContent');
    
    if (!dayData || !dayData.meals) {
        container.innerHTML = renderEmptyState();
        return;
    }
    
    const mealCount = Object.values(dayData.meals).reduce((sum, logs) => sum + (logs.length > 0 ? 1 : 0), 0);
    
    if (mealCount === 0) {
        container.innerHTML = renderEmptyState();
        return;
    }
    
    const progress = Math.min((dayData.logged / dayData.goal) * 100, 100);
    const progressColor = dayData.status === 'Over Goal' ? '#ff0000' : '#ff9800';
    const statusBg = dayData.status === 'Over Goal' ? '#ff0000' : '#4CAF50';
    
    container.innerHTML = `
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">${dayData.dayName}, ${dayData.month} ${dayData.dayNum}, ${new Date().getFullYear()}</h2>
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: ${statusBg}">
                    <i class="fas fa-check-circle mr-1"></i> ${dayData.status}
                </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-4">${mealCount} of 4 meals logged today</p>
            
            <div class="mb-8">
                <div class="flex justify-between items-baseline mb-3">
                    <div>
                        <span class="text-4xl font-bold text-gray-800">${dayData.logged}</span>
                        <span class="text-gray-600 text-lg ml-1">/ ${dayData.goal}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Remaining</p>
                        <p class="text-2xl font-bold" style="color: ${progressColor}">${Math.abs(dayData.remaining)}</p>
                    </div>
                </div>
                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-500 rounded-full" style="width: ${progress}%; background-color: ${progressColor}"></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                ${dayData.meals.Breakfast.length > 0 ? renderMealCard({type: 'Breakfast', totalCal: calculateTotalCalories(dayData.meals.Breakfast), foods: dayData.meals.Breakfast}) : ''}
                ${dayData.meals.Lunch.length > 0 ? renderMealCard({type: 'Lunch', totalCal: calculateTotalCalories(dayData.meals.Lunch), foods: dayData.meals.Lunch}) : ''}
                ${dayData.meals.Dinner.length > 0 ? renderMealCard({type: 'Dinner', totalCal: calculateTotalCalories(dayData.meals.Dinner), foods: dayData.meals.Dinner}) : ''}
                ${dayData.meals.Snacks.length > 0 ? renderMealCard({type: 'Snacks', totalCal: calculateTotalCalories(dayData.meals.Snacks), foods: dayData.meals.Snacks}) : ''}
            </div>
        </div>
    `;
}

function renderWeeklyOverview(weekData) {
    const container = document.getElementById('logContent');
    
    if (!weekData || weekData.length === 0) {
        container.innerHTML = renderErrorState('No data available for this week.');
        return;
    }
    
    const firstDate = new Date(weekData[0].date);
    const lastDate = new Date(weekData[weekData.length - 1].date);
    const dateRange = `${firstDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric' })} - ${lastDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
    
    container.innerHTML = `
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">${currentView === 'thisWeek' ? 'Weekly Overview' : 'Custom Date Range'}</h2>
                <p class="text-gray-600 font-medium">${dateRange}</p>
            </div>
            
            <div class="space-y-4">
                ${weekData.map(day => renderDayCard(day)).join('')}
            </div>
        </div>
    `;
}

function renderDayCard(day) {
    const hasLogs = day.logged > 0;
    const progress = Math.min((day.logged / day.goal) * 100, 100);
    const progressColor = day.status === 'Over Goal' ? '#ff0000' : '#ff9800';
    const statusBg = day.status === 'Over Goal' ? '#ff0000' : day.status === 'No Logs' ? '#d1dfd2' : '#4CAF50';
    const circleOpacity = hasLogs ? 'opacity-100' : 'opacity-40';
    
    return `
        <div class="bg-[#E8F5E9] rounded-2xl p-5 shadow-md hover:shadow-lg transition-all day-card">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-[#FFF9E6] ${circleOpacity} text-black flex flex-col items-center justify-center shadow-md">
                        <span class="text-2xl font-bold">${day.dayNum}</span>
                        <span class="text-xs uppercase">${day.month}</span>
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-800">${day.dayName}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white whitespace-nowrap ml-2" style="background-color: ${statusBg}">
                            ${day.status}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3 text-center text-sm mb-3">
                        <div>
                            <p class="text-gray-700 text-xs mb-1 font-medium">Goal</p>
                            <p class="font-bold text-gray-800">${day.goal}</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-xs mb-1 font-medium">Logged</p>
                            <p class="font-bold text-gray-800">${day.logged}</p>
                        </div>
                        <div>
                            <p class="text-gray-700 text-xs mb-1 font-medium">Remaining</p>
                            <p class="font-bold" style="color: ${progressColor}">${Math.abs(day.remaining)}</p>
                        </div>
                    </div>
                    
                    ${hasLogs ? `
                        <div class="h-2.5 bg-white rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" style="width: ${progress}%; background-color: ${progressColor}"></div>
                        </div>
                    ` : `
                        <div class="h-2.5 bg-white/50 rounded-full"></div>
                    `}
                </div>
                
                ${hasLogs ? `
                    <button onclick="showDayDetail('${day.date}')" class="flex-shrink-0 w-10 h-10 rounded-xl bg-white hover:bg-[#6aa84f] text-gray-600 hover:text-white flex items-center justify-center transition-all shadow-md">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                ` : ''}
            </div>
        </div>
    `;
}

function renderMealCard(meal) {
    const mealIcons = {
        'Breakfast': 'fa-sun',
        'Lunch': 'fa-mug-hot',
        'Dinner': 'fa-moon',
        'Snacks': 'fa-apple-alt'
    };
    
    const cardClasses = {
        'Breakfast': 'breakfast-card',
        'Lunch': 'lunch-card',
        'Dinner': 'dinner-card',
        'Snacks': 'snacks-card'
    };
    
    const iconClasses = {
        'Breakfast': 'breakfast-icon',
        'Lunch': 'lunch-icon',
        'Dinner': 'dinner-icon',
        'Snacks': 'snacks-icon'
    };
    
    const icon = mealIcons[meal.type] || 'fa-utensils';
    const cardClass = cardClasses[meal.type] || 'breakfast-card';
    const iconClass = iconClasses[meal.type] || 'breakfast-icon';
    
    return `
        <div class="meal-card ${cardClass}">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="meal-icon-circle ${iconClass}">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">${meal.type}</h3>
                        <p class="text-sm text-gray-600">${meal.totalCal} cal</p>
                    </div>
                </div>
                <button class="meal-add-button" onclick="window.location.href='/food-log'">
                    <i class="fas fa-plus"></i>
                    <span class="ml-2">Add Food</span>
                </button>
            </div>
            
            <div>
                ${meal.foods.map(food => `
                    <div class="food-item-row">
                        <div class="flex items-center gap-2 flex-1">
                            <span class="food-item-name">${food.foodName}</span>
                            ${food.isVerified ? `
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-blue-50 border border-blue-300 rounded text-[8px] font-bold text-blue-700">
                                    <i class="fas fa-check-circle text-[8px]"></i>
                                    ${food.source}
                                </span>
                            ` : ''}
                        </div>
                        <span class="food-item-calories">${Math.round(food.calories)} cal</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function renderEmptyState() {
    return `
        <div class="text-center py-24">
            <div class="mb-6">
                <i class="fas fa-utensils text-gray-300 text-8xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">No food logged yet</h3>
            <p class="text-gray-500 mb-8">Start tracking your meals to see your progress!</p>
            <button onclick="window.location.href='/food-log'" class="px-8 py-4 bg-[#f7941d] hover:bg-[#f5b461] text-white rounded-2xl font-bold text-lg transition-all shadow-lg inline-flex items-center gap-3">
                <i class="fas fa-plus-circle text-xl"></i> Add First Meal
            </button>
        </div>
    `;
}

function renderErrorState(message) {
    return `
        <div class="text-center py-24">
            <div class="mb-6">
                <i class="fas fa-exclamation-triangle text-orange-400 text-8xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">Unable to Load Data</h3>
            <p class="text-gray-500 mb-8">${message}</p>
            <button onclick="location.reload()" class="px-8 py-4 bg-[#6b9080] hover:bg-[#5a8070] text-white rounded-2xl font-bold text-lg transition-all shadow-lg inline-flex items-center gap-3">
                <i class="fas fa-redo text-xl"></i> Retry
            </button>
        </div>
    `;
}

function showErrorMessage(message) {
    const container = document.getElementById('logContent');
    if (container) {
        container.innerHTML = renderErrorState(message);
    }
}

// ====== MODAL FUNCTIONS ======

function showDayDetail(dateStr) {
    const dayData = weeklyData.find(d => d.date === dateStr);
    
    if (!dayData || !dayData.meals) return;
    
    const allLogs = [...dayData.meals.Breakfast, ...dayData.meals.Lunch, ...dayData.meals.Dinner, ...dayData.meals.Snacks];
    if (allLogs.length === 0) return;
    
    const modal = document.getElementById('dayDetailModal');
    const title = document.getElementById('modalDayTitle');
    const content = document.getElementById('dayDetailContent');
    
    const date = new Date(dayData.date);
    const formattedDate = date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    title.textContent = formattedDate;
    
    const progress = Math.min((dayData.logged / dayData.goal) * 100, 100);
    const progressColor = dayData.status === 'Over Goal' ? '#ff0000' : '#ff9800';
    const statusBg = dayData.status === 'Over Goal' ? '#ff0000' : '#4CAF50';
    
    content.innerHTML = `
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: ${statusBg}">
                    <i class="fas fa-check-circle mr-1"></i> ${dayData.status}
                </span>
            </div>
            
            <div class="mb-6">
                <div class="flex justify-between items-baseline mb-3">
                    <div>
                        <span class="text-3xl font-bold text-gray-800">${dayData.logged}</span>
                        <span class="text-gray-600 text-lg ml-1">/ ${dayData.goal}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Remaining</p>
                        <p class="text-xl font-bold" style="color: ${progressColor}">${Math.abs(dayData.remaining)}</p>
                    </div>
                </div>
                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-500 rounded-full" style="width: ${progress}%; background-color: ${progressColor}"></div>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-4">Food Log Details</h3>
            
            <div class="space-y-4">
                ${dayData.meals.Breakfast.length > 0 ? renderMealDetailCard({type: 'Breakfast', totalCal: calculateTotalCalories(dayData.meals.Breakfast), foods: dayData.meals.Breakfast}) : ''}
                ${dayData.meals.Lunch.length > 0 ? renderMealDetailCard({type: 'Lunch', totalCal: calculateTotalCalories(dayData.meals.Lunch), foods: dayData.meals.Lunch}) : ''}
                ${dayData.meals.Dinner.length > 0 ? renderMealDetailCard({type: 'Dinner', totalCal: calculateTotalCalories(dayData.meals.Dinner), foods: dayData.meals.Dinner}) : ''}
                ${dayData.meals.Snacks.length > 0 ? renderMealDetailCard({type: 'Snacks', totalCal: calculateTotalCalories(dayData.meals.Snacks), foods: dayData.meals.Snacks}) : ''}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function renderMealDetailCard(meal) {
    const mealIcons = {
        'Breakfast': 'fa-sun',
        'Lunch': 'fa-mug-hot',
        'Dinner': 'fa-moon',
        'Snacks': 'fa-apple-alt'
    };
    
    const mealColors = {
        'Breakfast': '#efc16b',
        'Lunch': '#ffc39f',
        'Dinner': '#66c8a7',
        'Snacks': '#ffb796'
    };
    
    const icon = mealIcons[meal.type] || 'fa-utensils';
    const color = mealColors[meal.type] || '#f7941d';
    
    return `
        <div class="bg-[#fef5e7] rounded-xl p-5 meal-detail-card" style="border-left: 4px solid ${color}">
            <div class="flex items-center gap-3 mb-3">
                <i class="fas ${icon} text-xl" style="color: ${color}"></i>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800">${meal.type}</h4>
                    <p class="text-sm text-gray-600 font-semibold">${meal.totalCal} cal</p>
                </div>
            </div>
            
            <div class="space-y-2 pl-8">
                ${meal.foods.map(food => `
                    <div class="flex justify-between items-center py-1.5">
                        <div class="flex items-center gap-2 flex-1">
                            <span class="text-gray-700">${food.foodName}</span>
                            ${food.isVerified ? `
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-blue-50 border border-blue-300 rounded text-[8px] font-bold text-blue-700">
                                    <i class="fas fa-check-circle text-[8px]"></i>
                                    ${food.source}
                                </span>
                            ` : ''}
                        </div>
                        <span class="font-semibold text-gray-800">${Math.round(food.calories)} cal</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function closeDayDetail() {
    const modal = document.getElementById('dayDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ====== HELPER FUNCTIONS ======

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ====== GLOBAL FUNCTIONS (called from HTML) ======

window.filterByDate = function(filter) {
    if (filter === 'today') {
        loadTodayView();
    } else if (filter === 'thisWeek') {
        loadThisWeekView();
    }
};

window.showDayDetail = showDayDetail;
window.closeDayDetail = closeDayDetail;

window.openCalendar = function() {
    const modal = document.getElementById('calendarModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Set default dates to today
    const today = new Date();
    const todayStr = formatDate(today);
    document.getElementById('dateFrom').value = todayStr;
    document.getElementById('dateTo').value = todayStr;
};

window.closeCalendar = function() {
    const modal = document.getElementById('calendarModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

window.updateDateRange = async function() {
    const fromDateStr = document.getElementById('dateFrom').value;
    const toDateStr = document.getElementById('dateTo').value;
    
    if (!fromDateStr || !toDateStr) {
        alert('Please select both dates');
        return;
    }
    
    const fromDate = new Date(fromDateStr + 'T00:00:00');
    const toDate = new Date(toDateStr + 'T00:00:00');
    
    if (fromDate > toDate) {
        alert('Start date must be before end date');
        return;
    }
    
    currentView = 'custom';
    updateFilterButtons('custom');
    
    try {
        // Show loading
        const container = document.getElementById('logContent');
        container.innerHTML = `
            <div class="text-center py-24">
                <div class="mb-6">
                    <i class="fas fa-spinner fa-spin text-[#6b9080] text-6xl"></i>
                </div>
                <p class="text-gray-600 text-lg">Loading logs...</p>
            </div>
        `;
        
        // Call backend API to get logs in date range
        const response = await fetch(`/api/profile/logs-in-range?start_date=${fromDateStr}&end_date=${toDateStr}`);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to load logs');
        }
        
        const logsData = result.data;
        
        // Generate summary for each day in range
        const filtered = [];
        let current = new Date(fromDate);
        
        while (current <= toDate) {
            const dateStr = formatDate(current);
            const meals = logsData[dateStr] || {
                'Breakfast': [],
                'Lunch': [],
                'Dinner': [],
                'Snacks': []
            };
            
            const allLogs = [...meals.Breakfast, ...meals.Lunch, ...meals.Dinner, ...meals.Snacks];
            const totalLogged = calculateTotalCalories(allLogs);
            const goal = 2000;
            const remaining = goal - totalLogged;
            
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            filtered.push({
                date: dateStr,
                dayName: dayNames[current.getDay()],
                dayNum: current.getDate(),
                month: months[current.getMonth()],
                goal: goal,
                logged: totalLogged,
                remaining: remaining,
                status: totalLogged === 0 ? 'No Logs' : totalLogged > goal ? 'Over Goal' : 'On Track',
                meals: meals
            });
            
            // Properly increment date
            current.setDate(current.getDate() + 1);
        }
        
        weeklyData = filtered;
        renderWeeklyOverview(filtered);
        closeCalendar();
        
    } catch (error) {
        console.error('Error loading custom date range:', error);
        const container = document.getElementById('logContent');
        container.innerHTML = renderErrorState('Unable to load logs for selected dates. Please try again.');
    }
};

