<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{$title??'Error!'}}</title>

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
            flex: 100%;
            flex-direction: column;
        }

        .top-flex {
            display: flex;
            flex-direction: row;
            margin-bottom: 10px;
            width: 100%;
        }

        .top-flex > * {
            display: flex;
            flex: 50%;
            flex-direction: row;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 2px solid;
            font-size: 26px;
            padding: 0 15px 0 15px;
            direction: rtl;
        }

        .message {
            font-size: 18px;
            text-align: left;
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
            border: 2px solid #dae1e7;
            text-decoration: none;
        }

        a::-moz-focus-inner {
            border-style: none;
            padding: 0;
        }

        a:-moz-focusring {
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
    <div class="top-flex">
        <div class="code">{{$code??500}}</div>
        <div class="message" style="padding: 10px;">No.<strong>{!! $errorNo??'000' !!}</strong></div>
    </div>
    <div id="message" style="display: none">{!! $message??'' !!}</div>
    <div>&nbsp;</div>
    <div>
        <a href="{{session()->previousUrl()}}">@lang('Go Back')</a>
        <a href="/">@lang('Go Home')</a>
    </div>
</div>
<script>

    document.addEventListener('keyup', (e) => {
        console.log(e.code);
        if (e.code === "KeyJ")
            document.getElementById('message').style.display='flex';

    });
</script>
</body>
</html>
