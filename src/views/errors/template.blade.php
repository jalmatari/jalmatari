
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 2px solid;
            font-size: 26px;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            text-align: center;
        }
        a:focus {
            outline: 1px dotted;
            outline: 5px auto -webkit-focus-ring-color;
        }
        a {
            background: transparent;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 100%;
            line-height: 1.15;
            margin: 0 .5rem;
            display: inline-block;
            border-radius: .5rem;
            font-weight: 700;
            padding: .25rem .5rem;
            color: #3d4852;
            border:2px solid #dae1e7;
            text-decoration: none;
        }

        a::-moz-focus-inner {
            border-style: none;
            padding: 0;
        }

        a:-moz-focusring{
            outline: 1px dotted ButtonText;
        }

        a:hover {
            border-color: #b8c2cc;
        }
    </style>
    @yield('head')
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="code">@yield('code')</div>
    <div class="message" style="padding: 10px;">@yield('message')</div>
</div>
</body>
</html>