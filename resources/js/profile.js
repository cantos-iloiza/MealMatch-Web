// resources/js/profile.js
import { auth, db, storage } from './config/firebase';
import { onAuthStateChanged } from 'firebase/auth';
import { collection, query, where, getDocs, doc, getDoc, orderBy, Timestamp } from 'firebase/firestore';

let currentUserId = null;
let currentDateFilter = 'thisWeek';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeProfile();
});

// Main initialization function
async function initializeProfile() {
    try {
        onAuthStateChanged(auth, async (user) => {
            if (user) {
                currentUserId = user.uid;
                await loadProfileData();
                await loadProgressData();
                await loadFoodLogHistory();
            } else {
                // For development: Load with mock data instead of redirecting
                console.log('No user authenticated - using mock data for development');
                currentUserId = 'mock-user-id';
                loadMockData();
            }
        });
    } catch (error) {
        console.error('Error initializing profile:', error);
        // Fallback to mock data if Firebase fails
        currentUserId = 'mock-user-id';
        loadMockData();
    }
}

// Load user profile data
async function loadProfileData() {
    try {
        const userDocRef = doc(db, 'users', currentUserId);
        const userDoc = await getDoc(userDocRef);
        
        if (userDoc.exists()) {
            const userData = userDoc.data();
            
            document.getElementById('userName').textContent = userData.name || 'User';
            document.getElementById('userEmail').textContent = userData.email || 'email@example.com';
            
            const profilePic = document.getElementById('profilePic');
            if (userData.photoURL) {
                profilePic.src = userData.photoURL;
            } else {
                profilePic.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userData.name || 'U')}&background=fda64a&color=fff&size=200&font-size=0.5&bold=true`;
            }
        }
    } catch (error) {
        console.error('Error loading profile data:', error);
        document.getElementById('userName').textContent = 'User';
        document.getElementById('userEmail').textContent = 'email@example.com';
    }
}

// Load progress data
async function loadProgressData() {
    try {
        // Calculate weekly streak
        const weeklyStreak = await calculateWeeklyStreak();
        updateStreakCircles(weeklyStreak);
        
        // Fetch highest streak
        const statsDocRef = doc(db, 'userStats', currentUserId);
        const statsDoc = await getDoc(statsDocRef);
        
        if (statsDoc.exists()) {
            const stats = statsDoc.data();
            document.getElementById('highestStreak').textContent = stats.highestStreak || 0;
        } else {
            document.getElementById('highestStreak').textContent = '0';
        }
        
        // Calculate average calories
        const avgCalories = await calculateAverageCalories();
        document.getElementById('avgCalories').textContent = Math.round(avgCalories);
        
    } catch (error) {
        console.error('Error loading progress data:', error);
        document.getElementById('highestStreak').textContent = '0';
        document.getElementById('avgCalories').textContent = '0';
    }
}

// Calculate weekly streak
async function calculateWeeklyStreak() {
    try {
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
        
        return uniqueDays.size;
    } catch (error) {
        console.error('Error calculating weekly streak:', error);
        return 0;
    }
}

// Update streak circles
function updateStreakCircles(streak) {
    const circles = document.querySelectorAll('.day-circle');
    circles.forEach((circle, index) => {
        if (index < streak) {
            circle.classList.remove('bg-white/50', 'text-gray-400');
            circle.classList.add('bg-[#fb7e00]', 'text-white');
        } else {
            circle.classList.remove('bg-[#fb7e00]', 'text-white');
            circle.classList.add('bg-white/50', 'text-gray-400');
        }
    });
    
    document.getElementById('streakText').textContent = `${streak} consecutive days this week`;
}

// Calculate average calories
async function calculateAverageCalories() {
    try {
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
        
        return daysWithLogs.size > 0 ? totalCalories / daysWithLogs.size : 0;
    } catch (error) {
        console.error('Error calculating average calories:', error);
        return 0;
    }
}

// Load food log history
async function loadFoodLogHistory() {
    try {
        const logs = await getFoodLogsForFilter(currentDateFilter);
        renderFoodLogs(logs);
    } catch (error) {
        console.error('Error loading food log history:', error);
        showEmptyState();
    }
}

// Get food logs based on filter
async function getFoodLogsForFilter(filter) {
    try {
        let startDate;
        const endDate = new Date();
        endDate.setHours(23, 59, 59, 999);
        
        if (filter === 'today') {
            startDate = new Date();
            startDate.setHours(0, 0, 0, 0);
            document.getElementById('logTitle').textContent = "Today's Log History";
        } else if (filter === 'thisWeek') {
            startDate = new Date();
            startDate.setDate(endDate.getDate() - 6);
            startDate.setHours(0, 0, 0, 0);
            document.getElementById('logTitle').textContent = "This Week's Log History";
        }
        
        const foodLogsRef = collection(db, 'foodLogs');
        const q = query(
            foodLogsRef,
            where('userId', '==', currentUserId),
            where('date', '>=', Timestamp.fromDate(startDate)),
            where('date', '<=', Timestamp.fromDate(endDate)),
            orderBy('date', 'desc')
        );
        
        const logsSnapshot = await getDocs(q);
        const logs = [];
        
        logsSnapshot.forEach(doc => {
            logs.push({ id: doc.id, ...doc.data() });
        });
        
        return groupLogsByDate(logs);
    } catch (error) {
        console.error('Error getting food logs:', error);
        return [];
    }
}

// Group logs by date
function groupLogsByDate(logs) {
    const grouped = {};
    
    logs.forEach(log => {
        const date = log.date.toDate().toDateString();
        if (!grouped[date]) {
            grouped[date] = {
                date: log.date.toDate(),
                goal: log.calorieGoal || 2000,
                meals: []
            };
        }
        
        grouped[date].meals.push({
            type: log.mealType || 'Meal',
            foods: log.foods || [],
            id: log.id
        });
    });
    
    return Object.values(grouped);
}

// Render food logs
function renderFoodLogs(logs) {
    const container = document.getElementById('logContent');
    
    if (logs.length === 0) {
        showEmptyState();
        return;
    }
    
    container.innerHTML = logs.map((log, dayIndex) => {
        const totalCalories = calculateDayCalories(log.meals);
        const progress = Math.min((totalCalories / log.goal) * 100, 100);
        const status = totalCalories > log.goal ? 'Over' : 'On Track';
        const statusColor = totalCalories > log.goal ? '#fb7e00' : '#93c47d';
        
        return `
            <div class="mb-6 last:mb-0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">${formatDate(log.date)}</h3>
                    <span class="px-4 py-1.5 rounded-full text-sm font-bold text-white" style="background-color: ${statusColor}">
                        ${status}
                    </span>
                </div>
                
                <p class="text-gray-700 text-sm mb-3">${log.meals.length} meals logged</p>
                
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="h-3 bg-white/50 rounded-full overflow-hidden">
                        <div class="h-full bg-[#93c47d] transition-all duration-500" style="width: ${progress}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span class="text-gray-700 font-semibold">${totalCalories} cal consumed</span>
                        <span class="text-gray-600">Goal: ${log.goal} cal</span>
                    </div>
                </div>
                
                <!-- Meals -->
                <div class="space-y-3">
                    ${renderMeals(log.meals)}
                </div>
            </div>
        `;
    }).join('');
}

// Render meals
function renderMeals(meals) {
    const mealIcons = {
        breakfast: 'fa-mug-hot',
        lunch: 'fa-bowl-food',
        dinner: 'fa-utensils',
        snacks: 'fa-cookie-bite'
    };
    
    const mealColors = {
        breakfast: '#ffcc3f',
        lunch: '#ffcc3f',
        dinner: '#79b4b0',
        snacks: '#fb7e00'
    };
    
    return meals.map(meal => {
        const mealType = meal.type.toLowerCase();
        const icon = mealIcons[mealType] || 'fa-utensils';
        const color = mealColors[mealType] || '#ffcc3f';
        const itemsCount = meal.foods.length;
        
        return `
            <div class="bg-white rounded-2xl p-5 meal-card" style="border-left: 6px solid ${color}">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-3">
                        <i class="fas ${icon} text-xl" style="color: ${color}"></i>
                        <h4 class="font-bold text-gray-800 text-lg">${capitalizeFirst(meal.type)}</h4>
                    </div>
                    <button onclick="window.location.href='/food-log'" class="px-4 py-2 bg-[#fda64a] hover:bg-[#fb7e00] text-white rounded-xl font-semibold text-sm transition-all">
                        ADD FOOD
                    </button>
                </div>
                
                <div class="space-y-2">
                    <p class="text-gray-600 text-sm font-medium">${itemsCount} Items</p>
                    ${meal.foods.length > 0 ? `
                        <div class="text-sm text-gray-700">
                            ${meal.foods.slice(0, 2).map(food => `
                                <div class="flex justify-between py-1">
                                    <span>• ${food.name || 'Unknown'}</span>
                                    <span class="font-semibold">${food.calories || 0} cal</span>
                                </div>
                            `).join('')}
                            ${meal.foods.length > 2 ? `<p class="text-gray-500 italic mt-1">+${meal.foods.length - 2} more items</p>` : ''}
                        </div>
                    ` : '<p class="text-gray-400 text-sm italic">No items yet</p>'}
                </div>
            </div>
        `;
    }).join('');
}

// Calculate day calories
function calculateDayCalories(meals) {
    return meals.reduce((total, meal) => {
        const mealTotal = meal.foods.reduce((sum, food) => sum + (food.calories || 0), 0);
        return total + mealTotal;
    }, 0);
}

// Show empty state
function showEmptyState() {
    const container = document.getElementById('logContent');
    container.innerHTML = `
        <div class="text-center py-20">
            <i class="fas fa-utensils text-gray-400 text-7xl mb-6 opacity-40"></i>
            <p class="text-gray-500 text-lg font-medium">No food logs yet</p>
            <p class="text-gray-400 text-sm mt-2 mb-6">Start logging your meals!</p>
            <button onclick="window.location.href='/food-log'" class="px-8 py-3 bg-[#fda64a] hover:bg-[#fb7e00] text-white rounded-xl font-bold transition-all">
                <i class="fas fa-plus mr-2"></i> Add First Meal
            </button>
        </div>
    `;
}

// Filter by date
window.filterByDate = async function(filter) {
    currentDateFilter = filter;
    
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-[#fda64a]', 'text-white');
        btn.classList.add('text-gray-700', 'bg-white');
    });
    
    if (filter === 'today') {
        document.getElementById('todayBtn').classList.add('active', 'bg-[#fda64a]', 'text-white');
    } else if (filter === 'thisWeek') {
        document.getElementById('thisWeekBtn').classList.add('active', 'bg-[#fda64a]', 'text-white');
    }
    
    await loadFoodLogHistory();
};

// Open calendar modal
window.openCalendar = function() {
    const modal = document.getElementById('calendarModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    const today = new Date();
    const weekAgo = new Date();
    weekAgo.setDate(today.getDate() - 6);
    
    document.getElementById('dateTo').valueAsDate = today;
    document.getElementById('dateFrom').valueAsDate = weekAgo;
};

// Close calendar modal
window.closeCalendar = function() {
    const modal = document.getElementById('calendarModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

// Update date range
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
        const logs = [];
        
        logsSnapshot.forEach(doc => {
            logs.push({ id: doc.id, ...doc.data() });
        });
        
        const groupedLogs = groupLogsByDate(logs);
        
        // Update title
        document.getElementById('logTitle').textContent = `${formatDate(fromDate)} - ${formatDate(toDate)}`;
        
        renderFoodLogs(groupedLogs);
        closeCalendar();
    } catch (error) {
        console.error('Error updating date range:', error);
        alert('Error loading logs for selected dates');
    }
};

// Utility functions
function formatDate(date) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Mock data loader for development/testing
function loadMockData() {
    // Load mock profile
    document.getElementById('userName').textContent = 'Juan Dela Cruz';
    document.getElementById('userEmail').textContent = 'juan.delacruz@email.com';
    
    const profilePic = document.getElementById('profilePic');
    profilePic.src = 'https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=fda64a&color=fff&size=200&font-size=0.5&bold=true';
    
    // Mock weekly streak (5 days)
    updateStreakCircles(5);
    
    // Mock highest streak
    document.getElementById('highestStreak').textContent = '21';
    
    // Mock average calories
    document.getElementById('avgCalories').textContent = '1850';
    
    // Load mock food logs
    loadMockFoodLogs();
}

function loadMockFoodLogs() {
    const container = document.getElementById('logContent');
    
    // Mock data for today
    const mockLogs = [
        {
            date: new Date(),
            goal: 2000,
            meals: [
                {
                    type: 'Breakfast',
                    foods: [
                        { name: 'Oatmeal', calories: 150 },
                        { name: 'Banana', calories: 105 },
                        { name: 'Coffee', calories: 5 }
                    ]
                },
                {
                    type: 'Lunch',
                    foods: [
                        { name: 'Chicken Breast', calories: 284 },
                        { name: 'Brown Rice', calories: 216 },
                        { name: 'Broccoli', calories: 55 }
                    ]
                },
                {
                    type: 'Dinner',
                    foods: [
                        { name: 'Salmon', calories: 367 },
                        { name: 'Sweet Potato', calories: 112 },
                        { name: 'Green Beans', calories: 44 }
                    ]
                },
                {
                    type: 'Snacks',
                    foods: [
                        { name: 'Apple', calories: 95 },
                        { name: 'Almonds', calories: 164 }
                    ]
                }
            ]
        }
    ];
    
    // Render mock logs
    container.innerHTML = mockLogs.map((log, dayIndex) => {
        const totalCalories = calculateDayCalories(log.meals);
        const progress = Math.min((totalCalories / log.goal) * 100, 100);
        const status = totalCalories > log.goal ? 'Over' : 'On Track';
        const statusColor = totalCalories > log.goal ? '#fb7e00' : '#93c47d';
        
        return `
            <div class="mb-6 last:mb-0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">${formatDate(log.date)}</h3>
                    <span class="px-4 py-1.5 rounded-full text-sm font-bold text-white" style="background-color: ${statusColor}">
                        ${status}
                    </span>
                </div>
                
                <p class="text-gray-700 text-sm mb-3">${log.meals.length} meals logged</p>
                
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="h-3 bg-white/50 rounded-full overflow-hidden">
                        <div class="h-full bg-[#93c47d] transition-all duration-500" style="width: ${progress}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm">
                        <span class="text-gray-700 font-semibold">${totalCalories} cal consumed</span>
                        <span class="text-gray-600">Goal: ${log.goal} cal</span>
                    </div>
                </div>
                
                <!-- Meals -->
                <div class="space-y-3">
                    ${renderMeals(log.meals)}
                </div>
            </div>
        `;
    }).join('');
}