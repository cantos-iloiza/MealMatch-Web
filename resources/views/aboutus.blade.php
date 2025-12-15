<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MealMatch</title>
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
            margin: 0 0 16px; 
            font-size: 24px;
            text-align: center;
            color: #333;
        }
        .content {
            width: 100%;
            min-height: 300px;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e2e2;
            font-size: 15px;
            line-height: 1.8;
            color: #333;
            background: #fffdf5;
        }
        .content p {
            margin-bottom: 16px;
        }
        .content ul {
            margin: 16px 0 16px 24px;
            list-style-type: disc;
        }
        .content li {
            margin-bottom: 8px;
        }
        .brand-name {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="app-header">
        <button class="back-btn" onclick="history.back()">←</button>
        <h1>About Us</h1>
    </div>
    <div class="container">
        <div class="card">
            <h2>About MealMatch</h2>
            <div class="content">
                <p><span class="brand-name">MealMatch</span> was developed as a health-focused mobile application designed to improve eating habits. Our mission is to help users make the most out of the ingredients they have, reduce food waste, and be mindful of their nutritional intake.</p>

                <p><span class="brand-name">MealMatch</span> combines technology with simplicity:</p>

                <ul>
                    <li>Ingredient-based recipe matching</li>
                    <li>Smart calorie tracking</li>
                    <li>Personalized meal recommendations</li>
                </ul>

                <p>We believe that eating healthier should be simple, accessible, and enjoyable for everyone.</p>
            </div>
        </div>
    </div>
</body>
</html>