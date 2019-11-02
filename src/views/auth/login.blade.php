@extends('site.template.template')
@section('content')
    <br><br><br>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('Log In')</div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route_('login') }}">
                            {{ csrf_field() }}
                            <?php $colName = $col->COLUMN_NAME; ?>
                            <div class="form-group{{ $errors->has($colName) ? ' has-error' : '' }}">
                                <label for="{{$colName}}"
                                       class="col-sm-4 control-label">@lang($col->TITLE)</label>
                                <div class="col-sm-8">
                                    <input id="{{$colName}}" type="{{$colName=='email'?'email':'text'}}"
                                           class="form-control" name="{{$colName}}"
                                           value="{{ old($colName) }}" required autofocus>

                                    @if ($errors->has($colName))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($colName) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-sm-4 control-label">@lang('Password')</label>

                                <div class="col-sm-8">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            @lang('Remember me')
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button type="submit" class="btn btn-primary">@lang('Log In')</button>
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="form-group">
                                    <ul class="col-sm-8 col-sm-offset-4 col-xs-offset-1">
                                        <li>
                                            <a class="btn-link" href="{{ route_('password.request') }}">
                                                @lang('Forgot account?')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{route_('register')}}" class="btn-link">
                                                @lang('New Account').
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
@endsection
