<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="{{Funs::Setting("site_meta")}}">
    <meta name="keywords" content="{{Funs::Setting("tags")}}">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php if (!isset($title))
        $title = '';
    $menues = Funs::SiteMenus();
    ?>
    <title>{{$title}} | {{Funs::Setting("site_title")}}</title>
    <script type="text/javascript">
        {!! 'var _globalObj ='. json_encode(['_token' => csrf_token()]).';' !!}
        var _globalUrl = '{{url('/')}}';
        var _API_URL = '{{route_('api')}}';
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">



    <link href="{{ asset('css/app.css?date=2019-3-24') }}" rel="stylesheet">
    <link href="{{ asset('css/bttn.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{url('/css/bootstrap/css/bootstrap-rtl.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/jalmatari/dist/css/animate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/jalmatari/dist/js/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" href="{{url('/css/jquery.jgrowl.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{url('/js/jssocials/jssocials.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{url('/js/jssocials/jssocials-theme-flat.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{url('/jalmatari/dist/js/cd-stretchy-nav/cd-style.css')}}"/>

    <script src="{{asset('jalmatari/dist/js/app.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/jamalFuns.js?date=5-1-2019')}}"></script>
    <script src="{{asset('jalmatari/dist/js/js.js?date=2019-3-24')}}"></script>
    <script src="{{asset('jalmatari/dist/js/jquery-cookie/jquery.cookie.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/sweetalert/sweetalert2.1.0.min.js')}}"></script>
    <script src="{{asset('jalmatari/plugins/noty/packaged/jquery.noty.packaged.min.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/jssocials/jssocials.min.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/social-likes.min.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/jquery.ns-autogrow.min.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/cd-stretchy-nav/cd-stretchy-nav-min.js')}}"></script>
    @include('site.facebook.fb-script')
    @yield('head')
    @yield('css')
</head>
<body class="{{ (Route::currentRouteName()!='home')?"inner":"home" }}">
<div class="body">
    <div class="header">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{route_('home')}}">
                        <img class="logo" src="{{url('/jalmatari/dist/img/logo.png')}}" alt="logo" height="40">
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                    <?php /*<ul class="menu top-menu nav navbar-nav">
                        @foreach($menues as $menu)
                            <li class="{{ (Funs::isCurrentMenu($menu->link)) ? 'active' : '' }}">
                                <a href="{{trim(route_($menu->link,null),'?') }}">
                                    {!! $menu->title !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>*/ ?>
                    <form class="navbar-form navbar-left" role="search" action="{{route_('search')}}">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="بحث..." name="q"
                                   value="{{request('q')}}">
                            <span class="input-group-btn">
							<button type="submit" class="btn btn-default btn-top-srch">
								<span class="glyphicon glyphicon-search">
									<span class="sr-only">بحث...</span>
								</span>
                            </button>
						</span>
                        </div>
                    </form>
                    <ul class="nav navbar-nav navbar-left">
                        @include('helpers.user_menu')
                    </ul>
                    <ul class="menu top-menu nav navbar-nav navbar-left site-title-nav hidden-xs hidden-sm">

                        <li class="dropdown">
                            <div class="site-title">{{Funs::Setting('site_title')}}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </div>
    <!-- .header -->
    <div class="under-header">

        @yield('under-header')
    </div>
    <div class="main-container">
        <div class="container main-content">
            @yield('content')
        </div>
    </div>
    <!-- .main-continer -->
    <nav class="cd-stretchy-nav">
        <a class="cd-nav-trigger" href="#0">
            Menu
            <span aria-hidden="true"></span>
        </a>

        <ul>
            <li><a href="/"><span>الرئيسية</span></a></li>
            @foreach($tadarsTypes as $tadarKey=>$tadarType)
                <li>
                    <a href="{{route_('tdbr.list',$tadarKey)}}" class="tdbr-menu-item menu-item-{{$tadarKey}}">
                        <span>{{$tadarType}}</span>
                    </a>
                </li>
            @endforeach
            <li><a href="{{route_('contact_us')}}"><span>تواصل معنا</span></a></li>
        </ul>

        <span aria-hidden="true" class="stretchy-nav-bg"></span>
    </nav>
    <div class="footer">
        <div class="container">
        </div>
    </div>
    <!-- .footer -->
</div>


@yield('end')
@if (session()->has('alert'))
    <script type="text/javascript">
        $(function () {
            alert('{{ session()->pull("alert") }}');
            {{session()->forget("alert")}}
        });
    </script>
@endif
<script type="text/javascript">
    $(function () {
        $(document).on({
            ajaxStart: function() { $('#loading-ajax').show();    },
            ajaxStop: function() { $('#loading-ajax').hide(); }
        });
    });
</script>
@yield('js')
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-53929623-5"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-53929623-5');
</script>
<div id="loading-ajax" style="display: none">
    <div class="center-vertical" >
    <svg width="200px"  height="200px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-ripple" style="background: none;"><circle cx="50" cy="50" r="38.9473" fill="none" ng-attr-stroke="@{{config.c1}}" ng-attr-stroke-width="@{{config.width}}" stroke="#813d25" stroke-width="2"><animate attributeName="r" calcMode="spline" values="0;40" keyTimes="0;1" dur="1" keySplines="0 0.2 0.8 1" begin="-0.5s" repeatCount="indefinite"></animate><animate attributeName="opacity" calcMode="spline" values="1;0" keyTimes="0;1" dur="1" keySplines="0.2 0 0.8 1" begin="-0.5s" repeatCount="indefinite"></animate></circle><circle cx="50" cy="50" r="22.8031" fill="none" ng-attr-stroke="@{{config.c2}}" ng-attr-stroke-width="@{{config.width}}" stroke="#ab6841" stroke-width="2"><animate attributeName="r" calcMode="spline" values="0;40" keyTimes="0;1" dur="1" keySplines="0 0.2 0.8 1" begin="0s" repeatCount="indefinite"></animate><animate attributeName="opacity" calcMode="spline" values="1;0" keyTimes="0;1" dur="1" keySplines="0.2 0 0.8 1" begin="0s" repeatCount="indefinite"></animate></circle></svg>

    </div>
</div>
</body>
</html>