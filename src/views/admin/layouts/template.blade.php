<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <?php if (!isset($title))
        $title = '';
    ?>
    <title>{{Funs::GetPageTitle($title)}} | {{Funs::Setting("site_title")}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">


    <script>
            {!! 'var _globalObj = '.  json_encode(['_token' => csrf_token()]).';' !!}
        var _globalUrl = '{{url('/')}}';
    </script>
    <script src="{{url('jalmatari/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <script src="{{url('jalmatari/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{url('jalmatari/plugins/fastclick/fastclick.min.js')}}"></script>
    <script src="{{url('jalmatari/dist/js/app.min.js')}}"></script>

    <script src="{{asset('jalmatari/dist/js/lodash.core.min.js')}}"></script>
    <script src="{{asset('jalmatari/dist/js/jamalFuns.js')}}"></script>


    <script src='{{url('jalmatari/plugins/moment/moment-with-locales.min.js')}}'></script>
    <link href="{{url('jalmatari/plugins/iCheck/all.css')}}" rel="stylesheet">
    <script src='{{url('jalmatari/plugins/iCheck/icheck.min.js')}}'></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{url('/jalmatari/plugins/jQueryUI/jquery-ui.min.js')}}"></script>
    <script src="{{url('jalmatari/dist/js/demo.js')}}"></script>
    <script src="{{url('jalmatari/dist/js/funs.js')}}"></script>
    <script src="{{url('jalmatari/plugins/noty/packaged/jquery.noty.packaged.min.js')}}"></script>

    <script src="{{url('jalmatari/plugins/jalmatari/js/elfinder.min.js')}}"></script>
    <script src="{{url('jalmatari/plugins/chosen/chosen.jquery.min.js')}}"></script>
    <script src="{{url('jalmatari/plugins/chosen/chosen.proto.min.js')}}"></script>
    <link rel="stylesheet" href="{{url('jalmatari/plugins/jalmatari/css/elfinder.min.css')}}">
    <link rel="stylesheet" href="{{url('jalmatari/dist/css/animate.css')}}">
    <?php /*<link rel="stylesheet" media="screen"
          href="https://code.jquery.com/ui/1.11.4/themes/pepper-grinder/jquery-ui.css"/>*/?>
    <link href="{{url('/jalmatari/plugins/jQueryUI/jquery-ui.min.css')}}" rel="stylesheet"/>
    <link href="{{url('/jalmatari/plugins/jQueryUI/jquery-ui.structure.min.css')}}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{url('jalmatari/plugins/jalmatari/css/theme.css')}}">
    <script src="{{url('jalmatari/plugins/jalmatari/js/i18n/elfinder.ar.js')}}"></script>

    <!-- Bootstrap 3.3.4 -->
    <link href="{{url('jalmatari/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>

    <!-- Bootstrap RTL 3.2.0 -->
    <link href="{{url('jalmatari/dist/css/bootstrap-rtl.min.css')}}" rel="stylesheet"/>
    <link href="{{url('jalmatari/dist/css/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet"/>

    <link href="{{url('jalmatari/plugins/chosen/chosen.min.css')}}" rel="stylesheet"/>
    <link href="{{url('jalmatari/dist/css/ionicons/css/ionicons.min.css')}}" rel="stylesheet"/>
    <link href="{{url('jalmatari/dist/css/AdminLTE.min.css')}}" rel="stylesheet"/>
    <link href="{{url('jalmatari/dist/css/skins/_all-skins.min.css')}}" rel="stylesheet"/>

    <link href="{{url('jalmatari/dist/css/rtl.css')}}" rel="stylesheet"/>
    <link href="{{url('jalmatari/dist/css/styles.css')}}" rel="stylesheet"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{{url('jalmatari/dist/js/html5shiv.min.js')}}"></script>
    <script src="{{url('jalmatari/dist/js/respond.min.js')}}"></script>
    <![endif]-->
    <style>
        @yield('style')
        @if(request('show-in-modal')==1||isset($showContentOnly))

            .main-header, .main-sidebar, .main-footer, .content-header, .box-header {
            display: none !important;
            margin: 0px !important;
        }

        html, body, .content-wrapper, .wrapper, .content {
            min-height: 100vh;
            min-width: 100vw;
            background-color: transparent !important;
            margin: 0px !important;
            padding: 0px;
        }
        @endif
    </style>
    @yield('css')
    @yield('head')
</head>
<body class="skin-{{Funs::FirstSetting('site_color')}} sidebar-mini">
<div class="wrapper">

    {!! viewCache_('adminHeader_'.auth()->id(),'admin.layouts.header') !!}
    @include('admin.layouts.mainSidebar')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{Funs::Setting("site_title")}}
                <small>{!! $title !!}</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/admin"><i class="fa fa-dashboard"></i></a></li>
                <li class="active"><a href="#">{!! Funs::GetPageTitle($title) !!}</a>
                </li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            @if (session()->has('alert'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> {!! session("alert") !!}.
                </div>
            @endif
            @yield('body')
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-left hidden-xs">
            <b>الإصدار </b> {{setting('ver')??'0.1'}}
        </div>
        <small>{{ microtime(true) - session('startMicroTime') }}</small>
        <!--strong>By: <a href="#">Company <span class="text-yellow">Name</span> Tech</a>.</strong-->
    </footer>

    <?php
    //@include('admin.layouts.controlSidebar')
    ?>
</div>
<!-- ./wrapper -->


@yield('end')

<div id="main-modal" class="modal">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(function () {

        @if(request('show-in-modal')==1)

        $('form').submit(function (e) {

            e.preventDefault(); // don't submit multiple times
            this.submit();

            window.parent.$('#main-modal').modal('hide');
        });
        @endif
    });
</script>
@yield('js')

</body>
</html>
