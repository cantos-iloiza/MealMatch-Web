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

// FAKE DATA - Mimics Flutter's meal_logs structure
function getFakeMealLogs() {
    return {
        '2025-12-08': {
            'Breakfast': [
                { id: 'log1', foodName: 'Scrambled Eggs', calories: 200, carbs: 2, proteins: 12, fats: 15, serving: '2 eggs', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-08T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log2', foodName: 'Whole Wheat Toast', calories: 150, carbs: 28, proteins: 6, fats: 2, serving: '2 slices', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-08T07:05:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log3', foodName: 'Chicken Salad', calories: 350, carbs: 15, proteins: 30, fats: 18, serving: '1 bowl', brand: '', category: 'Lunch', timestamp: new Date('2025-12-08T12:00:00'), isVerified: false, source: '' },
                { id: 'log4', foodName: 'Rice', calories: 200, carbs: 45, proteins: 4, fats: 0.5, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-08T12:10:00'), isVerified: true, source: 'USDA' }
            ],
            'Dinner': [
                { id: 'log5', foodName: 'Grilled Fish', calories: 400, carbs: 0, proteins: 45, fats: 22, serving: '200g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-08T18:00:00'), isVerified: true, source: 'OFF' },
                { id: 'log6', foodName: 'Vegetables', calories: 100, carbs: 20, proteins: 3, fats: 1, serving: '1 cup', brand: '', category: 'Dinner', timestamp: new Date('2025-12-08T18:15:00'), isVerified: true, source: 'USDA' }
            ],
            'Snacks': [
                { id: 'log7', foodName: 'Nuts', calories: 200, carbs: 8, proteins: 6, fats: 18, serving: '30g', brand: '', category: 'Snacks', timestamp: new Date('2025-12-08T15:00:00'), isVerified: false, source: '' },
                { id: 'log8', foodName: 'Fruit', calories: 250, carbs: 60, proteins: 2, fats: 0.5, serving: '1 apple', brand: '', category: 'Snacks', timestamp: new Date('2025-12-08T20:00:00'), isVerified: true, source: 'USDA' }
            ]
        },
        '2025-12-09': {
            'Breakfast': [
                { id: 'log9', foodName: 'Pancakes', calories: 300, carbs: 50, proteins: 8, fats: 8, serving: '3 pieces', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-09T07:30:00'), isVerified: false, source: '' },
                { id: 'log10', foodName: 'Maple Syrup', calories: 100, carbs: 26, proteins: 0, fats: 0, serving: '2 tbsp', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-09T07:35:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log11', foodName: 'Burger', calories: 500, carbs: 40, proteins: 28, fats: 25, serving: '1 burger', brand: 'McDonalds', category: 'Lunch', timestamp: new Date('2025-12-09T13:00:00'), isVerified: true, source: 'OFF' },
                { id: 'log12', foodName: 'Fries', calories: 200, carbs: 30, proteins: 3, fats: 10, serving: 'medium', brand: 'McDonalds', category: 'Lunch', timestamp: new Date('2025-12-09T13:05:00'), isVerified: true, source: 'OFF' }
            ],
            'Dinner': [
                { id: 'log13', foodName: 'Pasta', calories: 450, carbs: 70, proteins: 15, fats: 12, serving: '1 plate', brand: '', category: 'Dinner', timestamp: new Date('2025-12-09T19:00:00'), isVerified: false, source: '' },
                { id: 'log14', foodName: 'Garlic Bread', calories: 150, carbs: 20, proteins: 4, fats: 6, serving: '2 slices', brand: '', category: 'Dinner', timestamp: new Date('2025-12-09T19:10:00'), isVerified: false, source: '' }
            ],
            'Snacks': [
                { id: 'log15', foodName: 'Chips', calories: 250, carbs: 32, proteins: 3, fats: 15, serving: '1 bag', brand: 'Lays', category: 'Snacks', timestamp: new Date('2025-12-09T16:00:00'), isVerified: true, source: 'OFF' },
                { id: 'log16', foodName: 'Soda', calories: 150, carbs: 39, proteins: 0, fats: 0, serving: '355ml', brand: 'Coca-Cola', category: 'Snacks', timestamp: new Date('2025-12-09T16:05:00'), isVerified: true, source: 'OFF' }
            ]
        },
        '2025-12-10': {
            'Breakfast': [
                { id: 'log17', foodName: 'Oatmeal', calories: 150, carbs: 27, proteins: 5, fats: 3, serving: '1 bowl', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-10T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log18', foodName: 'Berries', calories: 70, carbs: 17, proteins: 1, fats: 0.5, serving: '1/2 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-10T07:05:00'), isVerified: true, source: 'USDA' },
                { id: 'log19', foodName: 'Honey', calories: 100, carbs: 26, proteins: 0, fats: 0, serving: '1 tbsp', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-10T07:10:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log20', foodName: 'Chicken Wrap', calories: 450, carbs: 45, proteins: 30, fats: 18, serving: '1 wrap', brand: '', category: 'Lunch', timestamp: new Date('2025-12-10T12:30:00'), isVerified: false, source: '' },
                { id: 'log21', foodName: 'Side Salad', calories: 150, carbs: 10, proteins: 3, fats: 8, serving: '1 bowl', brand: '', category: 'Lunch', timestamp: new Date('2025-12-10T12:40:00'), isVerified: false, source: '' }
            ],
            'Dinner': [
                { id: 'log22', foodName: 'Steak', calories: 500, carbs: 0, proteins: 50, fats: 30, serving: '200g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-10T19:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log23', foodName: 'Mashed Potatoes', calories: 200, carbs: 35, proteins: 4, fats: 6, serving: '1 cup', brand: '', category: 'Dinner', timestamp: new Date('2025-12-10T19:15:00'), isVerified: false, source: '' }
            ],
            'Snacks': [
                { id: 'log24', foodName: 'Protein Bar', calories: 200, carbs: 20, proteins: 20, fats: 8, serving: '1 bar', brand: 'Quest', category: 'Snacks', timestamp: new Date('2025-12-10T15:00:00'), isVerified: true, source: 'OFF' },
                { id: 'log25', foodName: 'Apple', calories: 100, carbs: 25, proteins: 0, fats: 0, serving: '1 medium', brand: '', category: 'Snacks', timestamp: new Date('2025-12-10T21:00:00'), isVerified: true, source: 'USDA' }
            ]
        },
        '2025-12-11': {
            'Breakfast': [
                { id: 'log26', foodName: 'Yogurt', calories: 150, carbs: 20, proteins: 10, fats: 4, serving: '1 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-11T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log27', foodName: 'Granola', calories: 130, carbs: 18, proteins: 3, fats: 5, serving: '1/4 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-11T07:05:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log28', foodName: 'Sushi Roll', calories: 350, carbs: 50, proteins: 15, fats: 10, serving: '8 pieces', brand: '', category: 'Lunch', timestamp: new Date('2025-12-11T13:00:00'), isVerified: false, source: '' },
                { id: 'log29', foodName: 'Miso Soup', calories: 150, carbs: 8, proteins: 8, fats: 6, serving: '1 bowl', brand: '', category: 'Lunch', timestamp: new Date('2025-12-11T13:10:00'), isVerified: true, source: 'OFF' }
            ],
            'Dinner': [
                { id: 'log30', foodName: 'Roast Chicken', calories: 500, carbs: 0, proteins: 55, fats: 28, serving: '250g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-11T18:30:00'), isVerified: true, source: 'USDA' },
                { id: 'log31', foodName: 'Roasted Vegetables', calories: 200, carbs: 30, proteins: 5, fats: 8, serving: '1.5 cups', brand: '', category: 'Dinner', timestamp: new Date('2025-12-11T18:45:00'), isVerified: false, source: '' }
            ],
            'Snacks': [
                { id: 'log32', foodName: 'Trail Mix', calories: 200, carbs: 18, proteins: 6, fats: 14, serving: '1/4 cup', brand: '', category: 'Snacks', timestamp: new Date('2025-12-11T16:00:00'), isVerified: false, source: '' },
                { id: 'log33', foodName: 'Orange', calories: 100, carbs: 23, proteins: 1, fats: 0, serving: '1 medium', brand: '', category: 'Snacks', timestamp: new Date('2025-12-11T20:30:00'), isVerified: true, source: 'USDA' }
            ]
        },
        '2025-12-12': {
            'Breakfast': [
                { id: 'log34', foodName: 'French Toast', calories: 250, carbs: 35, proteins: 10, fats: 8, serving: '2 slices', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-12T08:00:00'), isVerified: false, source: '' },
                { id: 'log35', foodName: 'Bacon', calories: 100, carbs: 0, proteins: 6, fats: 8, serving: '2 strips', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-12T08:10:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log36', foodName: 'Caesar Salad', calories: 400, carbs: 15, proteins: 25, fats: 28, serving: '1 large bowl', brand: '', category: 'Lunch', timestamp: new Date('2025-12-12T12:00:00'), isVerified: false, source: '' },
                { id: 'log37', foodName: 'Breadsticks', calories: 200, carbs: 35, proteins: 6, fats: 4, serving: '2 pieces', brand: '', category: 'Lunch', timestamp: new Date('2025-12-12T12:15:00'), isVerified: false, source: '' }
            ],
            'Dinner': [
                { id: 'log38', foodName: 'Pizza', calories: 600, carbs: 70, proteins: 25, fats: 25, serving: '3 slices', brand: 'Pizza Hut', category: 'Dinner', timestamp: new Date('2025-12-12T19:30:00'), isVerified: true, source: 'OFF' },
                { id: 'log39', foodName: 'Salad', calories: 100, carbs: 12, proteins: 2, fats: 5, serving: '1 bowl', brand: '', category: 'Dinner', timestamp: new Date('2025-12-12T19:45:00'), isVerified: false, source: '' }
            ],
            'Snacks': [
                { id: 'log40', foodName: 'Popcorn', calories: 150, carbs: 30, proteins: 3, fats: 3, serving: '3 cups', brand: '', category: 'Snacks', timestamp: new Date('2025-12-12T16:00:00'), isVerified: false, source: '' },
                { id: 'log41', foodName: 'Chocolate', calories: 150, carbs: 18, proteins: 2, fats: 9, serving: '1 bar', brand: 'Hersheys', category: 'Snacks', timestamp: new Date('2025-12-12T21:00:00'), isVerified: true, source: 'OFF' }
            ]
        },
        '2025-12-13': {
            'Breakfast': [],
            'Lunch': [],
            'Dinner': [],
            'Snacks': []
        },
        '2025-12-14': {
            'Breakfast': [
                { id: 'log42', foodName: 'Oatmeal with Blueberries', calories: 150, carbs: 27, proteins: 5, fats: 3, serving: '1 bowl', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log43', foodName: 'Banana', calories: 105, carbs: 27, proteins: 1, fats: 0, serving: '1 medium', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:10:00'), isVerified: true, source: 'USDA' },
                { id: 'log44', foodName: 'Black Coffee', calories: 5, carbs: 0, proteins: 0, fats: 0, serving: '1 cup', brand: '', category: 'Breakfast', timestamp: new Date('2025-12-14T07:15:00'), isVerified: true, source: 'USDA' }
            ],
            'Lunch': [
                { id: 'log45', foodName: 'Grilled Chicken Breast', calories: 284, carbs: 0, proteins: 53, fats: 6, serving: '200g', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:30:00'), isVerified: true, source: 'USDA' },
                { id: 'log46', foodName: 'Brown Rice', calories: 216, carbs: 45, proteins: 5, fats: 2, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:35:00'), isVerified: true, source: 'USDA' },
                { id: 'log47', foodName: 'Steamed Broccoli', calories: 55, carbs: 11, proteins: 4, fats: 0.5, serving: '1 cup', brand: '', category: 'Lunch', timestamp: new Date('2025-12-14T12:40:00'), isVerified: true, source: 'USDA' }
            ],
            'Dinner': [
                { id: 'log48', foodName: 'Baked Salmon', calories: 367, carbs: 0, proteins: 40, fats: 22, serving: '150g', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log49', foodName: 'Roasted Sweet Potato', calories: 112, carbs: 26, proteins: 2, fats: 0, serving: '1 medium', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:15:00'), isVerified: true, source: 'USDA' },
                { id: 'log50', foodName: 'Grilled Asparagus', calories: 40, carbs: 8, proteins: 4, fats: 0, serving: '6 spears', brand: '', category: 'Dinner', timestamp: new Date('2025-12-14T19:20:00'), isVerified: true, source: 'USDA' }
            ],
            'Snacks': [
                { id: 'log51', foodName: 'Apple', calories: 95, carbs: 25, proteins: 0, fats: 0, serving: '1 medium', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T15:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log52', foodName: 'Almonds (1 oz)', calories: 164, carbs: 6, proteins: 6, fats: 14, serving: '23 almonds', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T17:00:00'), isVerified: true, source: 'USDA' },
                { id: 'log53', foodName: 'Greek Yogurt', calories: 84, carbs: 6, proteins: 15, fats: 0, serving: '170g', brand: '', category: 'Snacks', timestamp: new Date('2025-12-14T20:00:00'), isVerified: true, source: 'USDA' }
            ]
        }
    };
}

// Helper: Calculate total calories from meal logs (matches Flutter's calculateTotalCalories)
function calculateTotalCalories(logs) {
    return logs.reduce((sum, log) => sum + log.calories, 0);
}

// Helper: Get logs grouped by category for a date (matches Flutter's getLogsGroupedByCategory)
function getLogsGroupedByCategory(dateStr) {
    const allLogs = getFakeMealLogs();
    return allLogs[dateStr] || {
        'Breakfast': [],
        'Lunch': [],
        'Dinner': [],
        'Snacks': []
    };
}

// ====== END FAKE DATA FUNCTIONS ======

// ====== REAL FIREBASE FUNCTIONS (COMMENTED) ======
/* 
 * ========================================
 * UNCOMMENT THIS SECTION TO USE REAL FIREBASE DATA
 * ========================================
 * Make sure you have:
 * 1. Uncommented Firebase imports at the top
 * 2. Set up firebase.js config file
 * 3. Updated currentUserId to use real auth
 * ========================================
 */

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

// Matches Flutter: Calculate weekly streak based on unique login days
async function loadStreakFromFirebase() {
    const today = new Date();
    const startOfWeek = new Date(today);
    // Get Sunday of current week
    const dayOfWeek = today.getDay(); // 0 = Sunday
    startOfWeek.setDate(today.getDate() - dayOfWeek);
    startOfWeek.setHours(0, 0, 0, 0);
    
    const mealLogsRef = collection(db, 'users', currentUserId, 'meal_logs');
    const q = query(
        mealLogsRef,
        where('date', '>=', _formatDate(startOfWeek)),
        where('date', '<=', _formatDate(today))
    );
    
    const logsSnapshot = await getDocs(q);
    const uniqueDays = new Set();
    
    logsSnapshot.forEach(doc => {
        const data = doc.data();
        uniqueDays.add(data.date);
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
    // Load highest streak from userStats (if you have this collection)
    // For now, calculate from meal_logs
    
    // Get user's calorie goal
    const userDocRef = doc(db, 'users', currentUserId);
    const userDoc = await getDoc(userDocRef);
    
    if (userDoc.exists()) {
        const userData = userDoc.data();
        // Assuming highest streak is stored in user document
        document.getElementById('highestStreak').textContent = userData.highestStreak || 0;
    }
    
    // Calculate average calories from last 30 days
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    
    const mealLogsRef = collection(db, 'users', currentUserId, 'meal_logs');
    const q = query(
        mealLogsRef,
        where('date', '>=', _formatDate(thirtyDaysAgo))
    );
    
    const logsSnapshot = await getDocs(q);
    const dailyTotals = {};
    
    logsSnapshot.forEach(doc => {
        const data = doc.data();
        const dateKey = data.date;
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = 0;
        }
        dailyTotals[dateKey] += data.calories || 0;
    });
    
    const days = Object.keys(dailyTotals);
    const totalCalories = Object.values(dailyTotals).reduce((sum, cal) => sum + cal, 0);
    const avgCalories = days.length > 0 ? Math.round(totalCalories / days.length) : 0;
    
    document.getElementById('avgCalories').textContent = avgCalories;
}

// Matches Flutter: getLogsGroupedByCategory(date)
async function loadLogsForDate(date) {
    const dateKey = _formatDate(date);
    
    // Check cache first
    if (foodLogsCache[dateKey]) {
        return foodLogsCache[dateKey];
    }
    
    try {
        const mealLogsRef = collection(db, 'users', currentUserId, 'meal_logs');
        const q = query(
            mealLogsRef,
            where('date', '==', dateKey)
        );
        
        const logsSnapshot = await getDocs(q);
        const grouped = {
            'Breakfast': [],
            'Lunch': [],
            'Dinner': [],
            'Snacks': []
        };
        
        logsSnapshot.forEach(doc => {
            const data = doc.data();
            const log = {
                id: doc.id,
                foodName: data.foodName || 'Unknown Food',
                calories: data.calories || 0,
                carbs: data.carbs || 0,
                proteins: data.proteins || 0,
                fats: data.fats || 0,
                serving: data.serving || '',
                brand: data.brand || '',
                category: data.category || 'Snacks',
                timestamp: data.timestamp?.toDate() || new Date(data.date),
                isVerified: data.isVerified || false,
                source: data.source || ''
            };
            
            if (grouped[log.category]) {
                grouped[log.category].push(log);
            }
        });
        
        // Sort by timestamp (newest first)
        Object.keys(grouped).forEach(category => {
            grouped[category].sort((a, b) => b.timestamp - a.timestamp);
        });
        
        foodLogsCache[dateKey] = grouped;
        return grouped;
        
    } catch (error) {
        console.error('Error loading logs for date:', dateKey, error);
        return {
            'Breakfast': [],
            'Lunch': [],
            'Dinner': [],
            'Snacks': []
        };
    }
}

// Matches Flutter: getLogsInRange(start, end)
async function loadLogsInRange(startDate, endDate) {
    try {
        // Generate all date strings in range
        const dateStrings = [];
        let current = new Date(startDate);
        const end = new Date(endDate);
        
        while (current <= end) {
            dateStrings.push(_formatDate(current));
            current.setDate(current.getDate() + 1);
        }
        
        // Firestore has limit of 10 for 'in' queries, so batch if needed
        const allLogs = {};
        
        for (const dateStr of dateStrings) {
            // Check cache first
            if (foodLogsCache[dateStr]) {
                allLogs[dateStr] = foodLogsCache[dateStr];
                continue;
            }
            
            const mealLogsRef = collection(db, 'users', currentUserId, 'meal_logs');
            const q = query(
                mealLogsRef,
                where('date', '==', dateStr)
            );
            
            const logsSnapshot = await getDocs(q);
            const grouped = {
                'Breakfast': [],
                'Lunch': [],
                'Dinner': [],
                'Snacks': []
            };
            
            logsSnapshot.forEach(doc => {
                const data = doc.data();
                const log = {
                    id: doc.id,
                    foodName: data.foodName || 'Unknown Food',
                    calories: data.calories || 0,
                    carbs: data.carbs || 0,
                    proteins: data.proteins || 0,
                    fats: data.fats || 0,
                    serving: data.serving || '',
                    brand: data.brand || '',
                    category: data.category || 'Snacks',
                    timestamp: data.timestamp?.toDate() || new Date(data.date),
                    isVerified: data.isVerified || false,
                    source: data.source || ''
                };
                
                if (grouped[log.category]) {
                    grouped[log.category].push(log);
                }
            });
            
            // Sort by timestamp
            Object.keys(grouped).forEach(category => {
                grouped[category].sort((a, b) => b.timestamp - a.timestamp);
            });
            
            foodLogsCache[dateStr] = grouped;
            allLogs[dateStr] = grouped;
        }
        
        return allLogs;
        
    } catch (error) {
        console.error('Error loading logs in range:', error);
        return {};
    }
}
*/

/* ========================================
 * END REAL FIREBASE FUNCTIONS
 * ======================================== */

// ====== VIEW SWITCHING FUNCTIONS ======

function loadTodayView() {
    currentView = 'today';
    updateFilterButtons('today');
    
    // FAKE DATA - Get today's data (Sunday, Dec 14)
    const todayData = getLogsGroupedByCategory('2025-12-14');
    
    // Calculate totals
    const allLogs = [...todayData.Breakfast, ...todayData.Lunch, ...todayData.Dinner, ...todayData.Snacks];
    const totalLogged = calculateTotalCalories(allLogs);
    const goal = 2000;
    const remaining = goal - totalLogged;
    const status = totalLogged > goal ? 'Over Goal' : 'On Track';
    
    renderTodayView({
        date: '2025-12-14',
        dayName: 'Sunday',
        dayNum: 14,
        month: 'Dec',
        goal: goal,
        logged: totalLogged,
        remaining: remaining,
        status: status,
        meals: todayData
    });
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    const today = new Date();
    await loadLogsForDate(today);
    const dateKey = _formatDate(today);
    const todayData = foodLogsCache[dateKey];
    
    const allLogs = [...todayData.Breakfast, ...todayData.Lunch, ...todayData.Dinner, ...todayData.Snacks];
    const totalLogged = calculateTotalCalories(allLogs);
    
    // Get user's goal from Firestore
    const userDocRef = doc(db, 'users', currentUserId);
    const userDoc = await getDoc(userDocRef);
    const goal = userDoc.exists() ? (userDoc.data().goalCalories || 2000) : 2000;
    
    const remaining = goal - totalLogged;
    const status = totalLogged > goal ? 'Over Goal' : 'On Track';
    
    renderTodayView({
        date: dateKey,
        dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][today.getDay()],
        dayNum: today.getDate(),
        month: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][today.getMonth()],
        goal: goal,
        logged: totalLogged,
        remaining: remaining,
        status: status,
        meals: todayData
    });
    */
}

function loadThisWeekView() {
    currentView = 'thisWeek';
    updateFilterButtons('thisWeek');
    
    // FAKE DATA - Generate weekly summary
    const fakeLogs = getFakeMealLogs();
    const weekDates = ['2025-12-08', '2025-12-09', '2025-12-10', '2025-12-11', '2025-12-12', '2025-12-13', '2025-12-14'];
    
    weeklyData = weekDates.map(dateStr => {
        const meals = fakeLogs[dateStr];
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
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    const today = new Date();
    const startOfWeek = new Date(today);
    const dayOfWeek = today.getDay();
    startOfWeek.setDate(today.getDate() - dayOfWeek);
    startOfWeek.setHours(0, 0, 0, 0);
    
    const endOfWeek = new Date(today);
    endOfWeek.setHours(23, 59, 59, 999);
    
    await loadLogsInRange(startOfWeek, endOfWeek);
    
    // Generate summary for each day
    const dateStrings = [];
    let current = new Date(startOfWeek);
    while (current <= endOfWeek) {
        dateStrings.push(_formatDate(current));
        current.setDate(current.getDate() + 1);
    }
    
    // Get user's goal
    const userDocRef = doc(db, 'users', currentUserId);
    const userDoc = await getDoc(userDocRef);
    const goal = userDoc.exists() ? (userDoc.data().goalCalories || 2000) : 2000;
    
    weeklyData = dateStrings.map(dateStr => {
        const meals = foodLogsCache[dateStr] || {Breakfast: [], Lunch: [], Dinner: [], Snacks: []};
        const allLogs = [...meals.Breakfast, ...meals.Lunch, ...meals.Dinner, ...meals.Snacks];
        const totalLogged = calculateTotalCalories(allLogs);
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
    const progressColor = day.status === 'Over Goal' ? '#ff0000' : '#ff9800';
    const statusBg = day.status === 'Over Goal' ? '#ff0000' : day.status === 'No Logs' ? '#d1dfd2' : '#4CAF50';
    const circleOpacity = hasLogs ? 'opacity-100' : 'opacity-40';
    
    return `
        <div class="bg-[#E8F5E9] rounded-2xl p-5 shadow-md hover:shadow-lg transition-all day-card">
            <div class="flex items-center gap-4">
                <!-- Date Circle -->
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-[#FFF9E6] ${circleOpacity} text-black flex flex-col items-center justify-center shadow-md">
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
                    <button onclick="showDayDetail('${day.date}')" class="flex-shrink-0 w-10 h-10 rounded-xl bg-white hover:bg-[#6aa84f] text-gray-600 hover:text-white flex items-center justify-center transition-all shadow-md">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                ` : ''}
            </div>
        </div>
    `;
}

// ====== MEAL CARD FUNCTION - MATCHES YOUR DESIGN ======
function renderMealCard(meal) {
    // Define meal icons
    const mealIcons = {
        'Breakfast': 'fa-sun',
        'Lunch': 'fa-mug-hot',
        'Dinner': 'fa-moon',
        'Snacks': 'fa-apple-alt'
    };
    
    // Define card background colors
    const cardClasses = {
        'Breakfast': 'breakfast-card',
        'Lunch': 'lunch-card',
        'Dinner': 'dinner-card',
        'Snacks': 'snacks-card'
    };
    
    // Define icon colors
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
            <!-- Header -->
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
            
            <!-- Food Items -->
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
    // FAKE DATA - Get day data
    const dayData = weeklyData.find(d => d.date === dateStr);
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    const dayData = weeklyData.find(d => d.date === dateStr);
    */
    
    if (!dayData || !dayData.meals) return;
    
    const allLogs = [...dayData.meals.Breakfast, ...dayData.meals.Lunch, ...dayData.meals.Dinner, ...dayData.meals.Snacks];
    if (allLogs.length === 0) return;
    
    const modal = document.getElementById('dayDetailModal');
    const title = document.getElementById('modalDayTitle');
    const content = document.getElementById('dayDetailContent');
    
    title.textContent = `${dayData.dayName}, December ${dayData.dayNum}`;
    
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

// Format date as YYYY-MM-DD (matches Flutter)
function _formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padLeft(2, '0');
    const day = String(date.getDate()).padLeft(2, '0');
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
    const fakeLogs = getFakeMealLogs();
    const filtered = [];
    
    let current = new Date(fromDate);
    while (current <= toDate) {
        const dateStr = _formatDate(current);
        const meals = fakeLogs[dateStr] || {Breakfast: [], Lunch: [], Dinner: [], Snacks: []};
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
    
    /* ====== FIREBASE VERSION (COMMENTED) ======
    try {
        fromDate.setHours(0, 0, 0, 0);
        toDate.setHours(23, 59, 59, 999);
        
        await loadLogsInRange(fromDate, toDate);
        
        // Generate summary for each day in range
        const dateStrings = [];
        let current = new Date(fromDate);
        while (current <= toDate) {
            dateStrings.push(_formatDate(current));
            current.setDate(current.getDate() + 1);
        }
        
        // Get user's goal
        const userDocRef = doc(db, 'users', currentUserId);
        const userDoc = await getDoc(userDocRef);
        const goal = userDoc.exists() ? (userDoc.data().goalCalories || 2000) : 2000;
        
        const filtered = dateStrings.map(dateStr => {
            const meals = foodLogsCache[dateStr] || {Breakfast: [], Lunch: [], Dinner: [], Snacks: []};
            const allLogs = [...meals.Breakfast, ...meals.Lunch, ...meals.Dinner, ...meals.Snacks];
            const totalLogged = calculateTotalCalories(allLogs);
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
        
        weeklyData = filtered;
        renderWeeklyOverview(filtered);
        closeCalendar();
    } catch (error) {
        console.error('Error updating date range:', error);
        alert('Error loading logs for selected dates');
    }
    */
};