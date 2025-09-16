<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Preview' }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f8fafc;
        }
        
        .preview-header {
            background: #3b82f6;
            color: white;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .preview-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        .content {
            background: white;
            padding: 40px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .entry-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1f2937;
        }
        
        .entry-content {
            font-size: 1.1rem;
            color: #374151;
        }
        
        .meta-info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="preview-header">
        <svg class="preview-icon" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
        </svg>
        Preview Mode - This is a draft version
    </div>
    
    <div class="content">
        @if(isset($title) || isset($content))
            <div class="meta-info">
                <strong>Collection:</strong> {{ ucfirst(str_replace('_', ' ', $slug ?? 'Unknown')) }}<br>
                @if(isset($date))
                    <strong>Date:</strong> {{ $date }}<br>
                @endif
                @if(isset($author))
                    <strong>Author:</strong> {{ $author }}
                @endif
            </div>
            
            @if(isset($title))
                <h1 class="entry-title">{{ $title }}</h1>
            @endif
            
            @if(isset($content))
                <div class="entry-content">
                    {!! $content !!}
                </div>
            @endif
        @else
            <h1 class="entry-title">Preview Content</h1>
            <div class="entry-content">
                <p>This is a preview of your draft content. The entry data will be displayed here once properly configured.</p>
                
                <h3>Available Data:</h3>
                <div class="meta-info">
                    @foreach($__data as $key => $value)
                        @if(!in_array($key, ['is_preview', 'preview_token']) && !is_array($value))
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>