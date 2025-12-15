<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - MealMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #FFF5CF;
            min-height: 100vh;
        }
        .app-header {
            background-color: #4CAF50;
            color: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        .back-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
            position: absolute;
            left: 20px;
        }
        .app-header h1 {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            flex: 1;
        }
        .container {
            padding: 16px;
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 16px;
            border: 2px solid #4CAF50;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            padding: 24px;
        }
        h2 { 
            margin: 0 0 20px; 
            font-size: 24px;
            text-align: center;
            color: #333;
        }
        .content {
            width: 100%;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e2e2;
            font-size: 15px;
            line-height: 1.8;
            color: #333;
            background: #fffdf5;
        }
        .content h3 {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #000;
        }
        .content h3:first-child {
            margin-top: 0;
        }
        .content h4 {
            font-size: 14px;
            font-weight: bold;
            margin: 12px 0 8px 0;
            color: #000;
        }
        .content p {
            margin-bottom: 12px;
            color: #444;
        }
        .content ul {
            margin: 8px 0 12px 32px;
            list-style-type: disc;
        }
        .content li {
            margin-bottom: 6px;
            color: #444;
        }
        .faq {
            margin-bottom: 16px;
        }
        .faq-q {
            font-weight: bold;
            color: #000;
            margin-bottom: 4px;
        }
        .faq-a {
            margin-left: 8px;
            color: #444;
        }
        .contact-email {
            text-align: center;
            color: #4CAF50;
            font-weight: bold;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="app-header">
        <button class="back-btn" onclick="history.back()">←</button>
        <h1>User Manual</h1>
    </div>
    <div class="container">
        <div class="card">
            <h2>User's Manual</h2>
            <div class="content">
                <h3>1. Introduction</h3>
                <p>MealMatch is a mobile application that simplifies cooking and promotes healthier eating. It helps users discover recipes based on available ingredients, log their meals, and track calorie intake.</p>

                <h3>2. System Requirements</h3>
                <ul>
                    <li>Android 8.0 (Oreo) or higher / iOS 13 or higher</li>
                    <li>At least 100 MB free storage</li>
                    <li>Internet connection</li>
                </ul>

                <h3>3. Installation Guide</h3>
                <ul>
                    <li>Download MealMatch from the App Store or Google Play Store.</li>
                    <li>Open the app and allow required permissions.</li>
                    <li>Wait until installation is complete.</li>
                </ul>

                <h3>4. Account Setup</h3>
                <p>Open the app and tap Sign Up. Enter the following details:</p>
                <ul>
                    <li>Preferred Name</li>
                    <li>Goal (Lose, Maintain, Gain, or Healthy Eating)</li>
                    <li>Activity Level (Not Very Active, Lightly Active, Active, Very Active)</li>
                    <li>Basic Info (Sex, Age, Height, Weight)</li>
                    <li>Dietary Preferences & Food Restrictions</li>
                    <li>Email & Password</li>
                </ul>
                <p>Confirm your account and proceed to Home/Dashboard.</p>

                <h3>5. Navigating the App Home/Dashboard</h3>
                <p>Displays Calorie Goal, Food Logged, Remaining Calories</p>
                <p>Panels: Meal Matcher, Log Food, Recent Recipes, Weight Progress</p>
                <p>Ingredient-based recipe search "What Can I Cook?" complete/partial match</p>
                <p>Recipe steps, calories, ratings, and smart timers</p>

                <h4>Log Food:</h4>
                <ul>
                    <li>Select meal (Breakfast/Lunch/Dinner/Snack)</li>
                    <li>Search food or choose from Favorites, My Recipes, or Recent</li>
                    <li>Adjust servings and log calories</li>
                </ul>

                <h4>Log History:</h4>
                <ul>
                    <li>View previous daily food logs</li>
                    <li>Displays calories per meal and daily total</li>
                </ul>

                <h4>User Profile:</h4>
                <ul>
                    <li>Avatar, name, progress, uploaded recipes</li>
                </ul>

                <h4>Settings:</h4>
                <ul>
                    <li>Edit profile, modify goals</li>
                    <li>Change password, delete account</li>
                </ul>

                <h3>6. Features Explained</h3>
                <ul>
                    <li>Food Calorie Tracker – look up calories of specific foods</li>
                    <li>Daily Calorie Calculator – goal tracking in real time</li>
                    <li>Recipe Posting & Saving – upload and save recipes</li>
                    <li>Smart Timer – assists during cooking steps</li>
                    <li>Discover Recipes – based on filters and preferences</li>
                </ul>

                <h3>7. Troubleshooting & FAQs</h3>
                
                <div class="faq">
                    <p class="faq-q">Q: I forgot my password. What do I do?</p>
                    <p class="faq-a">A: Go to Login > Forgot Password and follow instructions.</p>
                </div>

                <div class="faq">
                    <p class="faq-q">Q: Why can't I see recipes offline?</p>
                    <p class="faq-a">A: An internet connection is required to access recipe data.</p>
                </div>

                <div class="faq">
                    <p class="faq-q">Q: Can I adjust my calorie goal later?</p>
                    <p class="faq-a">A: Yes, go to Settings > Modify Goals.</p>
                </div>

                <div class="faq">
                    <p class="faq-q">Q: How do I delete my account?</p>
                    <p class="faq-a">A: Go to Settings > Delete Account.</p>
                </div>

                <h3>8. Contact & Support</h3>
                <p>For questions or issues, please contact:</p>
                <p class="contact-email">support@mealmatch.com</p>
            </div>
        </div>
    </div>
</body>
</html>