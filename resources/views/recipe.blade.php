<?php
// Get recipe ID from URL
$recipeId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$recipeId) {
    header('Location: index.php');
    exit;
}

// Fetch recipe from MealDB API
$apiUrl = "https://www.themealdb.com/api/json/v1/1/lookup.php?i=" . $recipeId;
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

if (!$data || !isset($data['meals'][0])) {
    header('Location: index.php');
    exit;
}

$recipe = $data['meals'][0];

// Extract ingredients and measurements
$ingredients = [];
for ($i = 1; $i <= 20; $i++) {
    $ingredient = $recipe["strIngredient$i"];
    $measure = $recipe["strMeasure$i"];
    
    if (!empty($ingredient) && trim($ingredient) != "") {
        $ingredients[] = [
            'ingredient' => $ingredient,
            'measure' => trim($measure)
        ];
    }
}

// Split instructions into steps
$instructions = explode("\r\n", $recipe['strInstructions']);
$instructions = array_filter($instructions, function($step) {
    return !empty(trim($step));
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['strMeal']); ?> - MealMatch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFF5CF;
            color: #333;
        }

        /* Header */
        header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo span:first-child {
            color: #FF6B35;
        }

        nav a {
            margin: 0 1rem;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav a:hover {
            color: #FF6B35;
        }

        /* Hero Section */
        .hero {
            background: white;
            padding: 3rem 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .hero-text h3 {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .hero-text h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .hero-text h1 span {
            color: #FF6B35;
        }

        .recipe-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .meta-icon {
            width: 40px;
            height: 40px;
            background: #FFF5CF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .meta-info h4 {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }

        .meta-info p {
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }

        .description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .tag {
            background: #FFF5CF;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #666;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Content Section */
        .content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .section h2 span {
            color: #FF6B35;
        }

        .ingredients-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .ingredient-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: #FFF5CF;
            border-radius: 8px;
        }

        .ingredient-item::before {
            content: "•";
            color: #FF6B35;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .instructions-section {
            grid-column: 1 / -1;
        }

        .instruction-step {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: #FFF5CF;
            border-radius: 10px;
        }

        .step-number {
            background: #FF6B35;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .step-text {
            line-height: 1.6;
            color: #333;
        }

        .video-section {
            grid-column: 1 / -1;
            text-align: center;
        }

        .video-container {
            margin-top: 1.5rem;
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 15px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 15px;
        }

        .back-button {
            display: inline-block;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: #FF6B35;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #E55A2B;
        }

        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
            }

            .content {
                grid-template-columns: 1fr;
            }

            .ingredients-list {
                grid-template-columns: 1fr;
            }

            .instructions-section,
            .video-section {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <span>FLAV</span><span style="color: #FF6B35;">ORIZ</span>
        </div>
        <nav>
            <a href="index.php">HOME</a>
            <a href="recipes.php">RECIPES</a>
            <a href="about.php">ABOUT</a>
        </nav>
    </header>

    <div class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h3>Let's Cook</h3>
                <h1><?php echo htmlspecialchars($recipe['strMeal']); ?></h1>

                <div class="recipe-meta">
                    <div class="meta-item">
                        <div class="meta-icon">🍽️</div>
                        <div class="meta-info">
                            <h4>Category</h4>
                            <p><?php echo htmlspecialchars($recipe['strCategory']); ?></p>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">🌍</div>
                        <div class="meta-info">
                            <h4>Cuisine</h4>
                            <p><?php echo htmlspecialchars($recipe['strArea']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="tags">
                    <?php if ($recipe['strCategory']): ?>
                        <span class="tag"><?php echo htmlspecialchars($recipe['strCategory']); ?></span>
                    <?php endif; ?>
                    <?php if ($recipe['strArea']): ?>
                        <span class="tag"><?php echo htmlspecialchars($recipe['strArea']); ?></span>
                    <?php endif; ?>
                    <?php if ($recipe['strTags']): 
                        $tags = explode(',', $recipe['strTags']);
                        foreach ($tags as $tag): ?>
                            <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>

            <div class="hero-image">
                <img src="<?php echo htmlspecialchars($recipe['strMealThumb']); ?>" alt="<?php echo htmlspecialchars($recipe['strMeal']); ?>">
            </div>
        </div>
    </div>

    <div class="content">
        <div class="section">
            <h2><span>Ingredients</span></h2>
            <div class="ingredients-list">
                <?php foreach ($ingredients as $item): ?>
                    <div class="ingredient-item">
                        <span><?php echo htmlspecialchars($item['measure'] . ' ' . $item['ingredient']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section instructions-section">
            <h2>Cooking <span>Instructions</span></h2>
            <?php 
            $stepNumber = 1;
            foreach ($instructions as $step): 
                if (!empty(trim($step))): ?>
                    <div class="instruction-step">
                        <div class="step-number"><?php echo str_pad($stepNumber, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="step-text"><?php echo htmlspecialchars($step); ?></div>
                    </div>
                <?php 
                $stepNumber++;
                endif;
            endforeach; ?>
        </div>

        <?php if (!empty($recipe['strYoutube'])): 
            // Extract YouTube video ID
            preg_match('/[?&]v=([^&]+)/', $recipe['strYoutube'], $matches);
            $videoId = $matches[1] ?? '';
            if ($videoId): ?>
                <div class="section video-section">
                    <h2>Video <span>Tutorial</span></h2>
                    <div class="video-container">
                        <iframe 
                            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            <?php endif;
        endif; ?>
    </div>

    <div style="text-align: center; padding-bottom: 3rem;">
        <a href="index.php" class="back-button">← Back to Home</a>
    </div>
</body>
</html>