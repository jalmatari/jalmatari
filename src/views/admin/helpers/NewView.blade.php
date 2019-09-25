@extends('admin.layouts.template')
@section('head')
    <script src='{{url('/')}}/jalmatari/plugins/ckeditor/ckeditor.js'></script>
    <link href="{{url('/')}}/jalmatari/plugins/iCheck/all.css" rel="stylesheet" type="text/css">
    <script src='{{url('/')}}/jalmatari/plugins/iCheck/icheck.min.js'></script>
    <script>
        if (typeof fileManagerUrl == "undefined")
            fileManagerUrl = '{{$name}}';
        $(function () {
            $('#status').removeAttr('required');
            $('input[required]:not([type="hidden"])').before('<strong class="text-red"> *</strong>');

            $('input').on('ifChanged', function (event) {
                name = $(this).attr('name');
                $('[name="' + name + '"][type="hidden"]').val($(this).iCheck('update')[0].checked ? 1 : 0)
            });

            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                autoclose: true
            });

            $('#editor,.editor').each(function () {
                inputId = $(this).attr('id');
                CKEDITOR.replace(inputId, {
                    language: 'ar',
                    filebrowserBrowseUrl: '{{route_('jalmatari.elfinder.ckeditor')}}'
                });

            });
        });
    </script>
@stop
@section('body')
    @if(isset($include_view))
        @include($include_view)
    @endif
    <?php     $posibleViews = [ "addView", "new_edit", "new", "edit", "add" ];
    ?>
    @foreach($posibleViews as $posibleView)
        @if(View::exists("admin.{$name}.{$posibleView}"))
            @include("admin.{$name}.{$posibleView}")
        @endif
    @endforeach
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{!! $title !!}</h3>

                <div class="box-tools pull-right">
                    @yield('buttons')
                </div>
            </div>
            {!!Funs::Form('open',[['route' =>$route,'role'=>"form"]])!!}
            <div class="box-body">
                <?php

                foreach ($rows as $key => $row):
                $otherPars = [];
                $otherPars = $row['other'];
                $otherPars['class'] = (isset($otherPars['class']) ? $otherPars['class'] : "") . " form-control";
                if ($row['type'] == 'hidden'):
                    if (isset($row['other']['class']))
                        $row['other']['class'] = $row['other']['class'] . " form-control form-" . $key;
                    else
                        $row['other']['class'] = " form-control form-" . $key;
                    if (!isset($row['other']['id']))
                        $row['other']['id'] = $key;
                    echo Funs::Form($row['type'], [ $key, $row['data'], $row['other'] ]);
                else:
                ?>
                <div
                    class="form-group {{$key.'-form-row '.(in_array($row['type'],['textarea','json'])?"col-md-12":"col-md-6")}}">
                    {!!Funs::Form('label',[$key, $row['title'] . ":"])!!}
                    @if($row['type']=='checkbox')
                        {!!Funs::Form('checkbox',[$key, "",$row['data'], $row['other']])!!}

                        {!!Funs::Form('hidden',[$key, $row['data'], ['class'=>'checkbox-hidden-val']])!!}
                    @elseif($row['type']=='select')
                        <?php
                        $selected = null;
                        if (isset($row['selected']))
                            $selected = $row['selected'];
                        if (isset($otherPars['multiple'])) {
                            $key .= '[]';
                            if (isset($row['selected'])) {
                                $selected = json_decode($row['selected']);
                            }
                            else if (isset($row['data']['selected']) || isset($row['data']['disabled'])) {
                                if (isset($row['data']['selected']))
                                    $selected = $row['data']['selected'];
                                if (isset($row['data']['disabled']))
                                    $otherPars += [ 'disabled' => $row['data']['disabled'] ];

                                $row['data'] = $row['data']['data'];
                            }


                        }
                        ?>
                        {!!Funs::Form('select',[$key, $row['data'],$selected, $otherPars])!!}
                    @else
                        {!!Funs::Form($row['type'],[$key, $row['data'], $otherPars])!!}
                    @endif
                </div>
                <?php
                endif;
                endforeach;
                ?>
                <div class="clearfix"></div>
                <div class="box-footer">
                    {!! Funs::Form('submit',['حفظ',["class"=>"btn btn-primary"]]) !!}
                </div>

            </div>
            {!!Funs::Form('close')!!}

            <div class="clearfix"></div>
        </div>
    </div>
@endsection
