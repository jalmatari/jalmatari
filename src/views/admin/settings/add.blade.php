
@section('body')
    <style>
        #name{
            direction: ltr;
        }
    </style>
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">إضافة خاصية جديدة إلى إعدادات الموقع</h3>

                <div class="box-tools pull-right">
                    @yield('buttons')
                </div>
            </div>
            {!!Funs::Form('open',[['route' =>'admin.settings.save','role'=>"form"]])!!}
            <div class="box-body">
                <?php
                $conter = 0;
                    $sections=[
                        'main'=>'الإعدادت الرئيسية',
                        'site'=>'إعدادت موقع الواجهة',
                    ];
                ?>
                @foreach($cols as $row)
                    <div class="form-group col-md-6">
                        {!! Funs::Form('label',[$row,$names[$conter++]]) !!}
                        @if($row=='type')
                            {!! Funs::Form('select',[$row,$type,null,['class'=>'form-control']]) !!}
                        @elseif($row=='section')
                            {!! Funs::Form('select',[$row,$sections,null,['class'=>'form-control']]) !!}
                        @elseif($row=='status')
                            {!! Funs::Form('checkbox',[$row,null,['class'=>'form-control']]) !!}
                        @else
                            {!! Funs::Form('text',[$row,null,['class'=>'form-control']]) !!}
                        @endif
                    </div>
                @endforeach

                <div class="clearfix"></div>
                <div class="box-footer">
                    {!! Funs::Form('submit',['حفظ',["class"=>"btn btn-primary"]]) !!}
                </div>


            </div>
            {!!Form::close()!!}


            <div class="clearfix"></div>
        </div>
    </div>

@stop