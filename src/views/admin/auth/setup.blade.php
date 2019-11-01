@extends('admin.layouts.template')
@section('head')
    <style>
        .chosen-container {
            width: 100% !important;
            margin-bottom: 10px;
        }
    </style>
@endsection
@section('body')
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('Setup Auth Pages')</h3>
            </div>
            <div class="box-body">
                <h4>اختر الحقول المطلوب عرضها عند تسجيل حساب جديد:</h4>
                <div id="register-cols">
                    @foreach($tables as $cols)
                        <h3 class="text-light-blue">{{$cols->first()->tableName}}</h3>
                        @foreach($cols as $col)
                            <div class="col-sm-4">
                                <input {!! in_array($col->COLUMN_NAME,$authRegisterCols)?"checked":'' !!}
                                       id="{{$col->tableName.'_'.$col->COLUMN_NAME}}"
                                       value="{{$col->COLUMN_NAME}}"
                                       type="checkbox">
                                <label for="{{$col->tableName.'_'.$col->COLUMN_NAME}}">
                                    {{$col->TITLE??$col->COLUMN_NAME}}
                                </label>
                            </div>
                        @endforeach
                        <div class="clearfix"></div>
                    @endforeach
                </div>
                <br><br>
                <h4>بيانات تسجيل دخول الموقع:</h4>
                {!! Funs::Form('select',['loginCols',$authList,$authLoginCols,['multiple'=>'multiple']]) !!}
                <div class="clearfix"></div>
                <br><br>
                <h4>بيانات تسجيل دخول لوحة التحكم:</h4>
                {!! Funs::Form('select',['adminLoginCols',$authList,$authAdminLoginCols,['multiple'=>'multiple']]) !!}
                <div class="clearfix"></div>
                <br><br>
                <div class="box-footer">
                    <a href="#" id="btn-save" class="btn btn-primary pull-left">@lang("Save")</a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('end')
    <script>
        $(function () {
            $('#btn-save').click(function () {
                let registerCols = [];
                let loginCols = [];
                let adminLoginCols = [];
                $('#register-cols input:checked').each(function () {
                    registerCols.push(this.value);
                });
                $('[name="loginCols"] option:selected').each(function () {
                    loginCols.push(this.value);
                });
                $('[name="adminLoginCols"] option:selected').each(function () {
                    adminLoginCols.push(this.value);
                });
                if (registerCols.length == 0 || loginCols.length == 0 || adminLoginCols.length == 0)
                    alert('يرجى إدخال على الأقل حقل واحد!');
                else
                    J.api({
                        registerCols: registerCols,
                        loginCols: loginCols,
                        adminLoginCols: adminLoginCols
                    }, colsSaved, '{{route_('admin.auth.setup')}}');
                return false;
            });
        });

        function colsSaved(data) {
            alert('تم حفظ البيانات بنجاح!');
        }
    </script>
@endsection
