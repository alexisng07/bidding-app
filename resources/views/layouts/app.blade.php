<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bidding')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 40px 16px;
            color: #1a1a1a;
        }
        .card {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .countdown-bar {
            background: #cfe3f7;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 18px;
        }
        .countdown-bar.ended { background: #f7d6d6; }
        .stage {
            padding: 48px 24px;
            text-align: center;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
        }
        .stage .amount { font-size: 28px; font-weight: 700; }
        .stage .label { color: #666; font-size: 15px; }
        .stage .winner { font-size: 22px; font-weight: 700; color: #1a7f37; }
        .action-bar {
            display: flex;
            gap: 8px;
            padding: 16px 20px;
            border-top: 1px solid #eee;
            flex-wrap: wrap;
        }
        .action-bar input[type="number"], .action-bar input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .action-bar input[type="number"] { flex: 1 1 110px; min-width: 90px; }
        .action-bar input[type="text"] { flex: 1 1 90px; min-width: 70px; }
        button {
            border: none;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-increment { background: #e8eef7; color: #1a1a1a; }
        .btn-bid { background: #1a56db; color: #fff; font-weight: 600; }
        .btn-bid:disabled { background: #a9b8d6; cursor: not-allowed; }
        .error { color: #c0392b; font-size: 13px; padding: 0 20px 12px; }
        h1 { max-width: 480px; margin: 0 auto 8px; font-size: 20px; }
        .product-desc { max-width: 480px; margin: 0 auto 20px; color: #555; font-size: 14px; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
