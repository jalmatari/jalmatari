<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        تسجيل الدخول
        | {{Funs::Setting("site_title")}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{url('/')}}/jalmatari/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Bootstrap RTL 3.2.0 -->
    <link href="{{url('/')}}/jalmatari/dist/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/jalmatari/dist/css/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>

    <!-- Theme style -->
    <link href="{{url('/')}}/jalmatari/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="{{url('/')}}/jalmatari/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="{{url('/')}}/jalmatari/dist/css/rtl.css" rel="stylesheet" type="text/css"/>
    <link href="{{url('/')}}/jalmatari/dist/css/styles.css" rel="stylesheet" type="text/css"/>
    <style>

        .login-logo a, .register-logo a {
            text-shadow: 2px 2px 0px #afafaf;
        }

        img {

            max-width: 100%;
        }
    </style>
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{route_('admin')}}">{{Funs::Setting("site_title")}}</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="loginPic" align="center">
            <a href="#" title=""><img src="{{url('/').Funs::Setting("logo")}}" alt=""/></a>
        </div>
        <p class="login-box-msg">
            تسجيل الدخول إلى لوحة التحكم بالموقع
        </p>
        {!! Form::open(['route'=>'admin.login']) !!}
        @if (old('username'))
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>خطأ:</strong> يرجى التأكد من صحة البيانات المدخلة!
            </div>
        @endif

        <div class="form-group has-feedback">
            {!! Form::label('username','إسم المستخدم:') !!}
            {!! Form::text('username',old('username'),['class'=>'form-control']) !!}
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            {!! Form::label('password','كلمة المرور:') !!}
            {!! Form::password('password',['class'=>'form-control']) !!}
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group">

            {!! Form::checkBox('remember',old('remember'),['class'=>'form-control']) !!}
            {!! Form::label('remember','تذكر بيانات التسجيل؟') !!}
        </div>

        <div class="row">
            <div class="col-xs-6">

                {!! Form::submit('تسجيل الدخول',array('class'=>"btn btn-primary btn-block btn-flat")) !!}
            </div>
            <!-- /.col -->
        </div>
        {!! Form::close() !!}

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{url('/')}}/jalmatari/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{url('/')}}/jalmatari/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- iCheck -->
<script src="{{url('/')}}/jalmatari/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>

