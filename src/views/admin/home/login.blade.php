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
        <form method="POST" action="{{ route_('admin.login') }}">
            {{ csrf_field() }}
            <?php $colName = $col->COLUMN_NAME; ?>
            <div class="form-group has-feedback{{ $errors->has($colName) ? ' has-error' : '' }}">
                <label for="{{$colName}}"
                       class="control-label">@lang($col->TITLE):</label>
                    <input id="{{$colName}}" type="{{$colName=='email'?'email':'text'}}"
                           class="form-control" name="{{$colName}}"
                           value="{{ old($colName) }}" required autofocus>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>

                    @if ($errors->has($colName))
                        <span class="help-block">
                            <strong>{{ $errors->first($colName) }}</strong>
                        </span>
                    @endif
            </div>
            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">@lang('Password'):</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group">
                <label class="remember">
                    <input type="checkbox"
                           id="remember"
                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                    @lang('Remember me')
                </label>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">@lang('Log In')</button>
                </div>
            </div>

        </form>

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

