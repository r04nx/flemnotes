<?php
require_once 'names.php';

// Generate some example routes
$exampleRoutes = NameGenerator::generateMultiple(8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FlemNotes</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .welcome-container {
            background: white;
            border-radius: 1rem;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .logo svg {
            fill: #6366f1;
        }
        
        .logo h1 {
            font-size: 2rem;
            color: #1f2937;
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .welcome-text h2 {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .welcome-text p {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #6366f1;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5558e3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .examples {
            margin-top: 2rem;
        }
        
        .examples h3 {
            font-size: 0.875rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .example-routes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .example-route {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            text-align: center;
            font-family: 'Courier New', monospace;
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid transparent;
        }
        
        .example-route:hover {
            background: #eef2ff;
            border-color: #6366f1;
            transform: translateY(-2px);
        }
        
        .features {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .feature-item svg {
            color: #10b981;
            flex-shrink: 0;
        }
        
        @media (max-width: 640px) {
            .welcome-container {
                padding: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .example-routes {
                grid-template-columns: 1fr;
            }
            
            .feature-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="logo">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>FlemNotes</h1>
        </div>
        
        <div class="welcome-text">
            <h2>Welcome to FlemNotes!</h2>
            <p>Create instant notes with memorable URLs. No signup required. Just pick a route and start writing!</p>
        </div>
        
        <div class="action-buttons">
            <a href="/<?php echo NameGenerator::generateRoute(2); ?>" class="btn btn-primary">
                🎲 Create Random Note
            </a>
            <a href="/mynote" class="btn btn-secondary">
                ✏️ Use /mynote
            </a>
        </div>
        
        <div class="examples">
            <h3>Try These Cool Routes</h3>
            <div class="example-routes">
                <?php foreach ($exampleRoutes as $route): ?>
                    <a href="/<?php echo $route; ?>" class="example-route">
                        /<?php echo $route; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="features">
            <div class="feature-list">
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Auto-save enabled
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Markdown support
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    File uploads
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Password protection
                </div>
            </div>
        </div>
    </div>
</body>
</html>
