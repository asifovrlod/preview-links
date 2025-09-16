<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Not Available</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .error-container {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }
        
        .error-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            color: #ef4444;
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }
        
        .error-message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <svg class="error-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        
        <h1 class="error-title">Preview Not Available</h1>
        <p class="error-message">{{ $message }}</p>
    </div>
</body>
</html>