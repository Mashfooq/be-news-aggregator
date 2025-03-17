<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Aggregator API</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #FDFDFC;
            color: #1b1b18;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 800px;
            width: 100%;
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        p {
            font-size: 1.1rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            color: #555;
        }
        
        .features {
            margin: 2rem 0;
        }
        
        .feature {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .feature-icon {
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            background-color: #1b1b18;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: #333;
        }
        
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0a0a0a;
                color: #EDEDEC;
            }
            
            .card {
                background-color: #161615;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            }
            
            p {
                color: #A1A09A;
            }
            
            .btn {
                background-color: #EDEDEC;
                color: #1b1b18;
            }
            
            .btn:hover {
                background-color: #d1d1ce;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <nav>
                <a href="/api/documentation" class="btn">API Documentation</a>
            </nav>
        </header>
        
        <div class="main-content">
            <div class="card">
                <h1>Welcome to News Aggregator API</h1>
                <p>A modern RESTful API for aggregating and delivering personalized news content based on user preferences.</p>
                
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">📰</div>
                        <div>Browse and search news articles from multiple sources</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🔍</div>
                        <div>Filter articles by category, source, and date</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">👤</div>
                        <div>User authentication with Laravel Sanctum</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">⭐</div>
                        <div>Save user preferences for news sources and categories</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">📱</div>
                        <div>Personalized news feed based on user preferences</div>
                    </div>
                </div>
                
                <a href="/api/documentation" class="btn">View API Documentation</a>
            </div>
        </div>
    </div>
</body>
</html> 