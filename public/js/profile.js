// resources/js/profile.js

// ====== FIREBASE IMPORTS - COMMENTED FOR NOW ======
// Uncomment these when ready to use Firebase
// import { auth, db, storage } from './config/firebase';
// import { onAuthStateChanged } from 'firebase/auth';
// import { collection, query, where, getDocs, doc, getDoc, orderBy, Timestamp } from 'firebase/firestore';
// ====== END FIREBASE IMPORTS ======

let currentUserId = null;
let currentView = 'today'; // 'today', 'thisWeek', 'custom'
let weeklyData = []; // Store weekly data globally
let foodLogsCache = {}; // Cache for meal logs by date (matches Flutter)

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page loaded - Using FAKE DATA for development');
    initializeProfile();
});

// Main initialization function
async function initializeProfile() {
    try {
        /* ====== FIREBASE AUTH - COMMENTED FOR NOW ======
         * Uncomment when ready to use Firebase authentication
         */
        
        // onAuthStateChanged(auth, async (user) => {
        //     if (user) {
        //         currentUserId = user.uid;
        //         await loadRealData();
        //     } else {
        //         window.location.href = '/login';
        //     }
        // });
        
        /* ====== END FIREBASE AUTH ====== */
        
        // FAKE DATA - Load mock data for development
        currentUserId = 'mock-user-123';
        loadFakeData();
        
    } catch (error) {
        console.error('Error initializing profile:', error);
        loadFakeData();
    }
}

// ====== FAKE DATA FUNCTIONS (For Development) ======

function loadFakeData() {
    loadTodayView();
    loadFakeProfile();
    loadFakeStreak();
    loadFakeStats();
}

function loadFakeProfile() {
    const name = 'Hermione';
    document.getElementById('userName').textContent = name;
    document.getElementById('userEmail').textContent = 'mealmatch03@email.com';
}

function loadFakeStreak() {
    const streakDays = 2; // Only 2 days this week (Sunday Dec 14 and Monday Dec 15)
    document.getElementById('streakText').textContent = `${streakDays} days logged`;
    
    // Update circles - only first 2 circles (S and M) should be orange
    const circles = document.querySelectorAll('.day-circle');
    circles.forEach((circle, index) => {
        if (index < 2) { // Only circles 1 and 2 (index 0 and 1)
            circle.classList.remove('bg-white/50', 'text-gray-400');
            circle.classList.add('bg-[#f7941d]', 'text-white');
        } else {
            circle.classList.remove('bg-[#f7941d]', 'text-white');
            circle.classList.add('bg-white/50', 'text-gray-400');
        }
    });
}

function loadFakeStats() {
    document.getElementById('highestStreak').textContent = '2';
    document.getElementById('avgCalories').textContent = '1850';
}

// FAKE DATA - Mimics Flutter's meal_logs structure
function getFakeMealLogs() {
    return {
        '2025-12-14': {
            'Breakfast': [
                { id: 'log1', foodName: 'Oatmeal with Blueberries', calories: 150, carbs: 27, proteins: 5, fats: 3, serving: '1 bowl', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log2', foodName: 'Banana', calories: 105, carbs: 27, proteins: 1, fats: 0, serving: '1 medium', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:10:00'), isVerified: true, source: 'USDA' },
                { id: 'log3', foodName: 'Black Coffee', calories: 5, carbs: 0, proteins: 0, fats: 0, serving: '1 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:15:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log4', foodName: 'Grilled Chicken Breast', calories: 284, carbs: 0, proteins: 53, fats: 6, serving: '200g', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:30:00'), isVerified: true, source: 'USDA' },
                { id: 'log5', foodName: 'Brown Rice', calories: 216, carbs: 45, proteins: 5, fats: 2, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:35:00'), isVerified: true, source: 'USDA' },
                { id: 'log6', foodName: 'Steamed Broccoli', calories: 55, carbs: 11, proteins: 4, fats: 0.5, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:40:00'), isVerified: true, source: 'USDA' }
            ],
            'Dinner': [
                { id: 'log7', foodName: 'Baked Salmon', calories: 367, carbs: 0, proteins: 40, fats: 22, serving: '150g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log8', foodName: 'Roasted Sweet Potato', calories: 112, carbs: 26, proteins: 2, fats: 0, serving: '1 medium', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:15:00'), isVerified: true, source: 'USDA' },
                { id: 'log9', foodName: 'Grilled Asparagus', calories: 40, carbs: 8, proteins: 4, fats: 0, serving: '6 spears', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:20:00'), isVerified: true, source: 'USDA' }
            ],
            'Snacks': [
                { id: 'log10', foodName: 'Apple', calories: 95, carbs: 25, proteins: 0, fats: 0, serving: '1 medium', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T15:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log11', foodName: 'Almonds (1 oz)', calories: 164, carbs: 6, proteins: 6, fats: 14, serving: '23 almonds', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T17:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log12', foodName: 'Greek Yogurt', calories: 84, carbs: 6, proteins: 15, fats: 0, serving: '170g', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T20:00:00'), isVerified: true, source: 'USDA' }
            ]
        },
        '2025-12-15': {
            'Breakfast': [
                { id: 'log13', foodName: 'Scrambled Eggs', calories: 200, carbs: 2, proteins: 12, fats: 15, serving: '2 eggs', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-15T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log14', foodName: 'Whole Wheat Toast', calories: 150, carbs: 28, proteins: 6, fats: 2, serving: '2 slices', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-15T07:05:00'), isVerified: true, source: 'USDA' },
                { id: 'log15', foodName: 'Orange Juice', calories: 110, carbs: 26, proteins: 2, fats: 0, serving: '1 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-15T07:10:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log16', foodName: 'Chicken Salad', calories: 350, carbs: 15, proteins: 30, fats: 18, serving: '1 bowl', brand: '', category: 'Lunch', timestamp: new Date('2025-12-15T12:00:00'), isVerified: false, source: '' },
                { id: 'log17', foodName: 'Rice', calories: 200, carbs: 45, proteins: 4, fats: 0.5, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-15T12:10:00'), isVerified: true, source: 'USDA' }
            ],
            'Dinner': [
                { id: 'log18', foodName: 'Grilled Fish', calories: 400, carbs: 0, proteins: 45, fats: 22, serving: '200g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-15T18:00:00'), isVerified: true, source: 'OFF' },
                { id: 'log19', foodName: 'Vegetables', calories: 100, carbs: 20, proteins: 3, fats: 1, serving: '1 cup', brand: '', category: 'Dinner', timestamp: new Date('2025-12-15T18:15:00'), isVerified: true, source: 'USDA' }
            ],
            'Snacks': [
                { id: 'log20', foodName: 'Nuts', calories: 200, carbs: 8, proteins: 6, fats: 18, serving: '30g', brand: '', category: 'Snacks', timestamp: new Date('2025-12-15T15:00:00'), isVerified: false, source: '' },
                { id: 'log21', foodName: 'Fruit', calories: 250, carbs: 60, proteins: 2, fats: 0.5, serving: '1 apple', brand: '', category: 'Snacks', timestamp: new Date('2025-12-15T20:00:00'), isVerified: true, source: 'USDA' }
            ]
        },
        '2025-12-10': {
            'Breakfast': [
                { id: 'log22', foodName: 'Pancakes', calories: 350, carbs: 60, proteins: 8, fats: 10, serving: '3 pancakes', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-10T08:00:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log23', foodName: 'Burger', calories: 550, carbs: 45, proteins: 30, fats: 25, serving: '1 burger', brand: '', category: 'Lunch', timestamp: new Date('2025-12-10T13:00:00'), isVerified: false, source: '' }
            ],
            'Dinner': [
                { id: 'log24', foodName: 'Pizza', calories: 600, carbs: 70, proteins: 25, fats: 22, serving: '3 slices', brand: '', category: 'Dinner', timestamp: new Date('2025-12-10T19:00:00'), isVerified: true, source: 'USDA' }
            ],
            'Snacks': []
        }
    };
}

// Helper: Calculate total calories from meal logs
function calculateTotalCalories(logs) {
    return logs.reduce((sum, log) => sum + log.calories, 0);
}

// Helper: Get logs grouped by category for a date
function getLogsGroupedByCategory(dateStr) {
    const allLogs = getFakeMealLogs();
    return allLogs[dateStr] || {
        'Breakfast': [],
        'Lunch': [],
        'Dinner': [],
        'Snacks': []
    };
}

// ====== VIEW SWITCHING FUNCTIONS ======

function loadTodayView() {
    currentView = 'today';
    updateFilterButtons('today');
    
    const todayData = getLogsGroupedByCategory('2025-12-15');
    
    const allLogs = [...todayData.Breakfast, ...todayData.Lunch, ...todayData.Dinner, ...todayData.Snacks];
    const totalLogged = calculateTotalCalories(allLogs);
    const goal = 2000;
    const remaining = goal - totalLogged;
    const status = totalLogged > goal ? 'Over Goal' : 'On Track';
    
    renderTodayView({
        date: '2025-12-15',
        dayName: 'Monday',
        dayNum: 15,
        month: 'Dec',
        goal: goal,
        logged: totalLogged,
        remaining: remaining,
        status: status,
        meals: todayData
    });
}

function loadThisWeekView() {
    currentView = 'thisWeek';
    updateFilterButtons('thisWeek');
    
    const fakeLogs = getFakeMealLogs();
    const weekDates = ['2025-12-14', '2025-12-15', '2025-12-16', '2025-12-17', '2025-12-18', '2025-12-19', '2025-12-20'];
    
    weeklyData = weekDates.map(dateStr => {
        const meals = fakeLogs[dateStr] || {
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
                <h2 class="text-2xl font-bold text-gray-800">${dayData.dayName}, December ${dayData.dayNum}, 2025</h2>
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
        container.innerHTML = renderEmptyState();
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

function _formatDate(date) {
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
    
    // Set default dates to today (Dec 15, 2025)
    const today = new Date('2025-12-15');
    const todayStr = _formatDate(today);
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
    
    const fromDate = new Date(fromDateStr);
    const toDate = new Date(toDateStr);
    
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
            const dateStr = _formatDate(current);
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
            
            current.setDate(current.getDate() + 1);
        }
        
        weeklyData = filtered;
        renderWeeklyOverview(filtered);
        closeCalendar();
        
    } catch (error) {
        console.error('Error loading custom date range:', error);
        alert('Error loading logs for selected dates. Please try again.');
        // Reload today view on error
        loadTodayView();
    }
};