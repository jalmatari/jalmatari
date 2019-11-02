@extends('site.template.template')

@section('content')
    <br><br><br>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang("New Account")</div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route_('register') }}">
                            {{ csrf_field() }}
                            @foreach($cols as $col)
                                <?php $colName = $col->COLUMN_NAME ?>
                                <div class="form-group{{ $errors->has($colName) ? ' has-error' : '' }}">
                                    <label for="{{$colName}}"
                                           class="col-md-4 control-label">
                                        @lang($col->TITLE)
                                    </label>
                                    <div class="col-md-6">
                                        <input id="{{$colName}}"
                                               type="{{$colName=='email'?'email':'text'}}"
                                               class="form-control" name="{{$colName}}"
                                               value="{{ old($colName) }}"
                                               required autofocus>
                                        @if ($errors->has($colName))
                                            <span class="help-block">
                                                <strong>{{ $errors->first($colName) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if(!is_null($password))
                                <?php $colName = $password->COLUMN_NAME ?>
                                <div class="form-group{{ $errors->has($colName) ? ' has-error' : '' }}">
                                    <label for="{{$colName}}"
                                           class="col-md-4 control-label">
                                        @lang($password->TITLE)
                                    </label>
                                    <div class="col-md-6">
                                        <input id="{{$colName}}"
                                               type="password"
                                               class="form-control"
                                               name="{{$colName}}"
                                               required>
                                        @if ($errors->has($colName))
                                            <span class="help-block">
                                                <strong>{{ $errors->first($colName) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="{{$colName}}-confirm"
                                           class="col-md-4 control-label">
                                        @lang("Confirm Password")
                                    </label>
                                    <div class="col-md-6">
                                        <input id="{{$colName}}-confirm"
                                               type="password"
                                               class="form-control"
                                               name="{{$colName}}_confirmation"
                                               required>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang("New Account")
                                    </button>
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
