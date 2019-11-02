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
                                        @if($col->inputType=='select')
                                            {!! Funs::Form('select',[$colName,$col->inputSource,$col->inputValue,['class'=>'form-control','required'=>'required']]) !!}
                                        @else
                                            <input id="{{$colName}}"
                                                   type="{{$col->inputType}}"
                                                   class="form-control" name="{{$colName}}"
                                                   value="{{ $col->inputValue }}"
                                                   required autofocus>
                                        @endif
                                        @if ($errors->has($colName))
                                            <span class="help-block">
                                                <strong>{{ $errors->first($colName) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($cols->where('COLUMN_NAME','password')->count())
                            <div class="form-group">
                                <label for="password-confirm"
                                       class="col-md-4 control-label">
                                    @lang("Confirm Password")
                                </label>
                                <div class="col-md-6">
                                    <input id="password-confirm"
                                           type="password"
                                           class="form-control"
                                           name="password_confirmation"
                                           value="{{old('password_confirmation')}}"
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
