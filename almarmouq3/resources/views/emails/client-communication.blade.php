<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 24px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .footer {
            margin-top: 32px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        {!! nl2br(e($bodyContent)) !!}

        <div class="footer">
            <p>{{ config('app.name') }} &bull; Private Client Relations</p>
        </div>
    </div>
</body>

</html>
