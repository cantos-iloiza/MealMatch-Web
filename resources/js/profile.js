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
    loadFakeProfile();
    loadFakeStreak();
    loadFakeStats();
    loadTodayView(); // Default view
}

function loadFakeProfile() {
    const name = 'Juan Dela Cruz';
    document.getElementById('userName').textContent = name;
    document.getElementById('userEmail').textContent = 'juandelacruz@email.com';
    
    // Set initials
    const initials = name.split(' ').map(n => n[0]).join('');
    document.getElementById('profileInitials').textContent = initials;
}

function loadFakeStreak() {
    const streakDays = 5;
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
}

function loadFakeStats() {
    document.getElementById('highestStreak').textContent = '21';
    document.getElementById('avgCalories').textContent = '1850';
}

function getFakeWeeklyData() {
    return [
        {
            date: '2025-12-08',
            dayName: 'Monday',
            dayNum: 8,
            month: 'Dec',
            goal: 2000,
            logged: 1850,
            remaining: 150,
            status: 'On Track',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 350,
                    foods: [
                        { name: 'Scrambled Eggs', calories: 200 },
                        { name: 'Whole Wheat Toast', calories: 150 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 550,
                    foods: [
                        { name: 'Chicken Salad', calories: 350 },
                        { name: 'Rice', calories: 200 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 500,
                    foods: [
                        { name: 'Grilled Fish', calories: 400 },
                        { name: 'Vegetables', calories: 100 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 450,
                    foods: [
                        { name: 'Nuts', calories: 200 },
                        { name: 'Fruit', calories: 250 }
                    ]
                }
            ]
        },
        {
            date: '2025-12-09',
            dayName: 'Tuesday',
            dayNum: 9,
            month: 'Dec',
            goal: 2000,
            logged: 2100,
            remaining: -100,
            status: 'Over Goal',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 400,
                    foods: [
                        { name: 'Pancakes', calories: 300 },
                        { name: 'Maple Syrup', calories: 100 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 700,
                    foods: [
                        { name: 'Burger', calories: 500 },
                        { name: 'Fries', calories: 200 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 600,
                    foods: [
                        { name: 'Pasta', calories: 450 },
                        { name: 'Garlic Bread', calories: 150 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 400,
                    foods: [
                        { name: 'Chips', calories: 250 },
                        { name: 'Soda', calories: 150 }
                    ]
                }
            ]
        },
        {
            date: '2025-12-10',
            dayName: 'Wednesday',
            dayNum: 10,
            month: 'Dec',
            goal: 2000,
            logged: 1920,
            remaining: 80,
            status: 'On Track',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 320,
                    foods: [
                        { name: 'Oatmeal', calories: 150 },
                        { name: 'Berries', calories: 70 },
                        { name: 'Honey', calories: 100 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 600,
                    foods: [
                        { name: 'Chicken Wrap', calories: 450 },
                        { name: 'Side Salad', calories: 150 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 700,
                    foods: [
                        { name: 'Steak', calories: 500 },
                        { name: 'Mashed Potatoes', calories: 200 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 300,
                    foods: [
                        { name: 'Protein Bar', calories: 200 },
                        { name: 'Apple', calories: 100 }
                    ]
                }
            ]
        },
        {
            date: '2025-12-11',
            dayName: 'Thursday',
            dayNum: 11,
            month: 'Dec',
            goal: 2000,
            logged: 1780,
            remaining: 220,
            status: 'On Track',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 280,
                    foods: [
                        { name: 'Yogurt', calories: 150 },
                        { name: 'Granola', calories: 130 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 500,
                    foods: [
                        { name: 'Sushi Roll', calories: 350 },
                        { name: 'Miso Soup', calories: 150 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 700,
                    foods: [
                        { name: 'Roast Chicken', calories: 500 },
                        { name: 'Roasted Vegetables', calories: 200 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 300,
                    foods: [
                        { name: 'Trail Mix', calories: 200 },
                        { name: 'Orange', calories: 100 }
                    ]
                }
            ]
        },
        {
            date: '2025-12-12',
            dayName: 'Friday',
            dayNum: 12,
            month: 'Dec',
            goal: 2000,
            logged: 1950,
            remaining: 50,
            status: 'On Track',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 350,
                    foods: [
                        { name: 'French Toast', calories: 250 },
                        { name: 'Bacon', calories: 100 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 600,
                    foods: [
                        { name: 'Caesar Salad', calories: 400 },
                        { name: 'Breadsticks', calories: 200 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 700,
                    foods: [
                        { name: 'Pizza', calories: 600 },
                        { name: 'Salad', calories: 100 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 300,
                    foods: [
                        { name: 'Popcorn', calories: 150 },
                        { name: 'Chocolate', calories: 150 }
                    ]
                }
            ]
        },
        {
            date: '2025-12-13',
            dayName: 'Saturday',
            dayNum: 13,
            month: 'Dec',
            goal: 2000,
            logged: 0,
            remaining: 2000,
            status: 'No Logs',
            meals: []
        },
        {
            date: '2025-12-14',
            dayName: 'Sunday',
            dayNum: 14,
            month: 'Dec',
            goal: 2000,
            logged: 1597,
            remaining: 403,
            status: 'On Track',
            meals: [
                {
                    type: 'Breakfast',
                    totalCal: 260,
                    foods: [
                        { name: 'Oatmeal with Blueberries', calories: 150 },
                        { name: 'Banana', calories: 105 },
                        { name: 'Black Coffee', calories: 5 }
                    ]
                },
                {
                    type: 'Lunch',
                    totalCal: 555,
                    foods: [
                        { name: 'Grilled Chicken Breast', calories: 284 },
                        { name: 'Brown Rice', calories: 216 },
                        { name: 'Steamed Broccoli', calories: 55 }
                    ]
                },
                {
                    type: 'Dinner',
                    totalCal: 639,
                    foods: [
                        { name: 'Baked Salmon', calories: 367 },
                        { name: 'Roasted Sweet Potato', calories: 112 },
                        { name: 'Grilled Asparagus', calories: 40 },
                        { name: 'Green Salad', calories: 120 }
                    ]
                },
                {
                    type: 'Snacks',
                    totalCal: 343,
                    foods: [
                        { name: 'Apple', calories: 95 },
                        { name: 'Almonds (1 oz)', calories: 164 },
                        { name: 'Greek Yogurt', calories: 84 }
                    ]
                }
            ]
        }
    ];
}

// ====== END FAKE DATA FUNCTIONS ======

// ====== REAL FIREBASE FUNCTIONS (COMMENTED) ======

/* 
async function loadRealData() {
    try {
        await loadProfileFromFirebase();
        await loadStreakFromFirebase();
        await loadStatsFromFirebase();
        loadTodayView();
    } catch (error) {
        console.error('Error loading real data:', error);
    }
}

async function loadProfileFromFirebase() {
    const userDocRef = doc(db, 'users', currentUserId);
    const userDoc = await getDoc(userDocRef);
    
    if (userDoc.exists()) {
        const userData = userDoc.data();
        const name = userData.name || 'User';
        
        document.getElementById('userName').textContent = name;
        document.getElementById('userEmail').textContent = userData.email || 'email@example.com';
        
        const initials = name.split(' ').map(n => n[0]).join('');
        document.getElementById('profileInitials').textContent = initials;
    }
}

async function loadStreakFromFirebase() {
    const today = new Date();
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay());
    startOfWeek.setHours(0, 0, 0, 0);
    
    const loginRecordsRef = collection(db, 'loginRecords');
    const q = query(
        loginRecordsRef,
        where('userId', '==', currentUserId),
        where('loginDate', '>=', Timestamp.fromDate(startOfWeek))
    );
    
    const loginDocs = await getDocs(q);
    const uniqueDays = new Set();
    
    loginDocs.forEach(doc => {
        const date = doc.data().loginDate.toDate();
        uniqueDays.add(date.toDateString());
    });
    
    const streakDays = uniqueDays.size;
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
}

async function loadStatsFromFirebase() {
    // Load highest streak
    const statsDocRef = doc(db, 'userStats', currentUserId);
    const statsDoc = await getDoc(statsDocRef);
    
    if (statsDoc.exists()) {
        const stats = statsDoc.data();
        document.getElementById('highestStreak').textContent = stats.highestStreak || 0;
    }
    
    // Calculate average calories
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    thirtyDaysAgo.setHours(0, 0, 0, 0);
    
    const foodLogsRef = collection(db, 'foodLogs');
    const q = query(
        foodLogsRef,
        where('userId', '==', currentUserId),
        where('date', '>=', Timestamp.fromDate(thirtyDaysAgo))
    );
    
    const foodLogs = await getDocs(q);
    let totalCalories = 0;
    const daysWithLogs = new Set();
    
    foodLogs.forEach(doc => {
        const data = doc.data();
        totalCalories += data.totalCalories || 0;
        daysWithLogs.add(data.date.toDate().toDateString());
    });
    
    const avgCalories = daysWithLogs.size > 0 ? Math.round(totalCalories / daysWithLogs.size) : 0;
    document.getElementById('avgCalories').textContent = avgCalories;
}
*/

// ====== END REAL FIREBASE FUNCTIONS ======

// ====== VIEW SWITCHING FUNCTIONS ======

function loadTodayView() {
    currentView = 'today';
    updateFilterButtons('today');
    
    // FAKE DATA - Get today's data (Sunday, Dec 14)
    weeklyData = getFakeWeeklyData();
    const todayData = weeklyData.find(d => d.date === '2025-12-14');
    
    renderTodayView(todayData);
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const endOfDay = new Date();
    endOfDay.setHours(23, 59, 59, 999);
    
    const foodLogsRef = collection(db, 'foodLogs');
    const q = query(
        foodLogsRef,
        where('userId', '==', currentUserId),
        where('date', '>=', Timestamp.fromDate(today)),
        where('date', '<=', Timestamp.fromDate(endOfDay))
    );
    
    const logs = await getDocs(q);
    // Process and render...
    */
}

function loadThisWeekView() {
    currentView = 'thisWeek';
    updateFilterButtons('thisWeek');
    
    // FAKE DATA
    weeklyData = getFakeWeeklyData();
    renderWeeklyOverview(weeklyData);
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    const startOfWeek = new Date();
    startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
    startOfWeek.setHours(0, 0, 0, 0);
    
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);
    
    const foodLogsRef = collection(db, 'foodLogs');
    const q = query(
        foodLogsRef,
        where('userId', '==', currentUserId),
        where('date', '>=', Timestamp.fromDate(startOfWeek)),
        where('date', '<=', Timestamp.fromDate(endOfWeek)),
        orderBy('date', 'asc')
    );
    
    const logs = await getDocs(q);
    // Group by day and render...
    */
}

function updateFilterButtons(active) {
    const todayBtn = document.getElementById('todayBtn');
    const thisWeekBtn = document.getElementById('thisWeekBtn');
    
    // Reset all
    todayBtn.classList.remove('bg-[#6b9080]', 'text-white', 'active');
    todayBtn.classList.add('bg-white', 'text-gray-700');
    
    thisWeekBtn.classList.remove('bg-[#6b9080]', 'text-white', 'active');
    thisWeekBtn.classList.add('bg-white', 'text-gray-700');
    
    // Set active
    if (active === 'today') {
        todayBtn.classList.remove('bg-white', 'text-gray-700');
        todayBtn.classList.add('bg-[#6b9080]', 'text-white', 'active');
    } else if (active === 'thisWeek') {
        thisWeekBtn.classList.remove('bg-white', 'text-gray-700');
        thisWeekBtn.classList.add('bg-[#6b9080]', 'text-white', 'active');
    }
}

// ====== RENDER FUNCTIONS ======

function renderTodayView(dayData) {
    const container = document.getElementById('logContent');
    
    if (!dayData || dayData.meals.length === 0) {
        container.innerHTML = renderEmptyState();
        return;
    }
    
    const progress = Math.min((dayData.logged / dayData.goal) * 100, 100);
    const progressColor = dayData.status === 'Over Goal' ? '#e8674a' : '#6b9080';
    const statusBg = dayData.status === 'Over Goal' ? '#e8674a' : '#6b9080';
    
    container.innerHTML = `
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">${dayData.dayName}, December ${dayData.dayNum}, 2025</h2>
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: ${statusBg}">
                    <i class="fas fa-check-circle mr-1"></i> ${dayData.status}
                </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-4">${dayData.meals.length} of 4 meals logged today</p>
            
            <!-- Calories Progress -->
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
            
            <!-- Meals Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${dayData.meals.map(meal => renderMealCard(meal)).join('')}
            </div>
        </div>
    `;
}

function renderWeeklyOverview(weekData) {
    const container = document.getElementById('logContent');
    
    const dateRange = `December 8 - 14, 2025`;
    
    container.innerHTML = `
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Weekly Overview</h2>
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
    const progressColor = day.status === 'Over Goal' ? '#e8674a' : '#6b9080';
    const statusBg = day.status === 'Over Goal' ? '#e8674a' : day.status === 'No Logs' ? '#d1dfd2' : '#6b9080';
    const circleOpacity = hasLogs ? 'opacity-100' : 'opacity-40';
    
    return `
        <div class="bg-[#fef5e7] rounded-2xl p-5 shadow-md hover:shadow-lg transition-all day-card">
            <div class="flex items-center gap-4">
                <!-- Date Circle -->
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-[#6b9080] ${circleOpacity} text-white flex flex-col items-center justify-center shadow-md">
                        <span class="text-2xl font-bold">${day.dayNum}</span>
                        <span class="text-xs uppercase">${day.month}</span>
                    </div>
                </div>
                
                <!-- Day Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-800">${day.dayName}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white whitespace-nowrap ml-2" style="background-color: ${statusBg}">
                            ${day.status}
                        </span>
                    </div>
                    
                    <!-- Stats Row -->
                    <div class="grid grid-cols-3 gap-3 text-center text-sm mb-3">
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Goal</p>
                            <p class="font-bold text-gray-800">${day.goal}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Logged</p>
                            <p class="font-bold text-gray-800">${day.logged}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs mb-1">Remaining</p>
                            <p class="font-bold" style="color: ${progressColor}">${Math.abs(day.remaining)}</p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    ${hasLogs ? `
                        <div class="h-2.5 bg-white rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" style="width: ${progress}%; background-color: ${progressColor}"></div>
                        </div>
                    ` : `
                        <div class="h-2.5 bg-white/50 rounded-full"></div>
                    `}
                </div>
                
                <!-- Arrow Button -->
                ${hasLogs ? `
                    <button onclick="showDayDetail('${day.date}')" class="flex-shrink-0 w-10 h-10 rounded-xl bg-white hover:bg-[#6b9080] text-gray-600 hover:text-white flex items-center justify-center transition-all shadow-md">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                ` : ''}
            </div>
        </div>
    `;
}

function renderMealCard(meal) {
    const mealIcons = {
        'Breakfast': 'fa-mug-hot',
        'Lunch': 'fa-bowl-food',
        'Dinner': 'fa-moon',
        'Snacks': 'fa-apple-alt'
    };
    
    const mealColors = {
        'Breakfast': '#f5b461',
        'Lunch': '#f7941d',
        'Dinner': '#6b9080',
        'Snacks': '#e8674a'
    };
    
    const icon = mealIcons[meal.type] || 'fa-utensils';
    const color = mealColors[meal.type] || '#f7941d';
    
    return `
        <div class="bg-white rounded-2xl p-5 shadow-md hover:shadow-lg transition-all" style="border-left: 5px solid ${color}">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <i class="fas ${icon} text-2xl" style="color: ${color}"></i>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">${meal.type}</h3>
                        <p class="text-sm text-gray-600">${meal.totalCal} cal</p>
                    </div>
                </div>
                <button onclick="window.location.href='/food-log'" class="px-4 py-2 bg-[#f7941d] hover:bg-[#f5b461] text-white rounded-xl font-semibold text-sm transition-all shadow-md">
                    <i class="fas fa-plus mr-1"></i> Add
                </button>
            </div>
            
            <div class="space-y-2">
                ${meal.foods.map(food => `
                    <div class="flex justify-between items-center text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700">• ${food.name}</span>
                        <span class="font-semibold text-gray-800">${food.calories} cal</span>
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
    if (!dayData || dayData.meals.length === 0) return;
    
    const modal = document.getElementById('dayDetailModal');
    const title = document.getElementById('modalDayTitle');
    const content = document.getElementById('dayDetailContent');
    
    title.textContent = `${dayData.dayName}, December ${dayData.dayNum}`;
    
    const progress = Math.min((dayData.logged / dayData.goal) * 100, 100);
    const progressColor = dayData.status === 'Over Goal' ? '#e8674a' : '#6b9080';
    const statusBg = dayData.status === 'Over Goal' ? '#e8674a' : '#6b9080';
    
    content.innerHTML = `
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="px-4 py-2 rounded-full text-sm font-bold text-white" style="background-color: ${statusBg}">
                    <i class="fas fa-check-circle mr-1"></i> ${dayData.status}
                </span>
            </div>
            
            <!-- Calories Progress -->
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
            
            <!-- Meals List -->
            <div class="space-y-4">
                ${dayData.meals.map(meal => renderMealDetailCard(meal)).join('')}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function renderMealDetailCard(meal) {
    const mealIcons = {
        'Breakfast': 'fa-mug-hot',
        'Lunch': 'fa-bowl-food',
        'Dinner': 'fa-moon',
        'Snacks': 'fa-apple-alt'
    };
    
    const mealColors = {
        'Breakfast': '#f5b461',
        'Lunch': '#f7941d',
        'Dinner': '#6b9080',
        'Snacks': '#e8674a'
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
                        <span class="text-gray-700">${food.name}</span>
                        <span class="font-semibold text-gray-800">${food.calories} cal</span>
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
    
    // Set default dates
    const today = new Date();
    const weekAgo = new Date();
    weekAgo.setDate(today.getDate() - 6);
    
    document.getElementById('dateTo').valueAsDate = today;
    document.getElementById('dateFrom').valueAsDate = weekAgo;
};

window.closeCalendar = function() {
    const modal = document.getElementById('calendarModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

window.updateDateRange = async function() {
    const fromDate = new Date(document.getElementById('dateFrom').value);
    const toDate = new Date(document.getElementById('dateTo').value);
    
    if (!fromDate || !toDate || isNaN(fromDate.getTime()) || isNaN(toDate.getTime())) {
        alert('Please select both dates');
        return;
    }
    
    if (fromDate > toDate) {
        alert('Start date must be before end date');
        return;
    }
    
    currentView = 'custom';
    updateFilterButtons('custom');
    
    // FAKE DATA - Filter by date range
    const filtered = weeklyData.filter(day => {
        const dayDate = new Date(day.date);
        return dayDate >= fromDate && dayDate <= toDate;
    });
    
    renderWeeklyOverview(filtered);
    closeCalendar();
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    try {
        fromDate.setHours(0, 0, 0, 0);
        toDate.setHours(23, 59, 59, 999);
        
        const foodLogsRef = collection(db, 'foodLogs');
        const q = query(
            foodLogsRef,
            where('userId', '==', currentUserId),
            where('date', '>=', Timestamp.fromDate(fromDate)),
            where('date', '<=', Timestamp.fromDate(toDate)),
            orderBy('date', 'desc')
        );
        
        const logsSnapshot = await getDocs(q);
        // Process and group by day...
        
        closeCalendar();
    } catch (error) {
        console.error('Error updating date range:', error);
        alert('Error loading logs for selected dates');
    }
    */
};