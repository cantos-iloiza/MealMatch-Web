<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MealMatch</title>
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(to bottom, #FFE4CD, #CAECB4);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { color: #E78315; }
        .info { margin: 1rem 0; }
        button {
            background: linear-gradient(to right, #FB7E00, #FFCC3F);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to MealMatch!</h1>
        <div class="info">
            <p><strong>Email:</strong> {{ session('firebase_email') }}</p>
            <p><strong>Name:</strong> {{ session('firebase_name') ?? 'N/A' }}</p>
        </div>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>