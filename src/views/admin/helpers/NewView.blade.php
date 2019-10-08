@extends('admin.layouts.template')
@section('head')
    <script src='{{url('/')}}/jalmatari/plugins/ckeditor/ckeditor.js'></script>
    <link href="{{url('/')}}/jalmatari/plugins/iCheck/all.css" rel="stylesheet" type="text/css">
    <script src='{{url('/')}}/jalmatari/plugins/iCheck/icheck.min.js'></script>
    <style>
        .ui-datepicker-title > select {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            margin: 2px 2px !important;
        }

    </style>
    <script>
        let datepickerOptions = {
            dateFormat: 'yy-mm-dd',
            autoclose: true,
            changeMonth: true,
            changeYear: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            beforeShow: function (input, inst) {
                // Handle calendar position before showing it.
                // It's not supported by Datepicker itself (for now) so we need to use its internal variables.
                var calendar = inst.dpDiv;
                // Dirty hack, but we can't do anything without it (for now, in jQuery UI 1.8.20)
                setTimeout(function () {
                    calendar.css('z-index', '1030')
                    if ($(input).offset().top >= 300)//only if the input not on the top the page
                    {
                        calendar.position({
                            my: 'left bottom',
                            at: 'left top',
                            collision: 'none',
                            of: input
                        });
                    }
                }, 1);
            }
        };


        if (typeof fileManagerUrl == "undefined")
            fileManagerUrl = '{{$name}}';
        $(function () {

            (function (factory) {
                if (typeof define === "function" && define.amd) {

                    // AMD. Register as an anonymous module.
                    define(["../widgets/datepicker"], factory);
                } else {

                    // Browser globals
                    factory(jQuery.datepicker);
                }
            }(function (datepicker) {

                datepicker.regional.ar = {
                    closeText: "إغلاق",
                    prevText: "&#x3C;السابق",
                    nextText: "التالي&#x3E;",
                    currentText: "اليوم",
                    monthNames: ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
                        "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"],
                    monthNamesShort: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
                    dayNames: ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"],
                    dayNamesShort: ["أحد", "اثنين", "ثلاثاء", "أربعاء", "خميس", "جمعة", "سبت"],
                    dayNamesMin: ["أحد", "اثنين", "ثلاثاء", "أربعاء", "خميس", "جمعة", "سبت"],
                    weekHeader: "أسبوع",
                    dateFormat: "dd/mm/yy",
                    firstDay: 6,
                    isRTL: true,
                    showMonthAfterYear: false,
                    yearSuffix: ""
                };
                datepicker.setDefaults(datepicker.regional.ar);

                return datepicker.regional.ar;

            }));

            $('#status').removeAttr('required');
            $('input[required]:not([type="hidden"])').before('<strong class="text-red"> *</strong>');

            $('input').on('ifChanged', function (event) {
                name = $(this).attr('name');
                $('[name="' + name + '"][type="hidden"]').val($(this).iCheck('update')[0].checked ? 1 : 0)
            });

            $('.datepicker').datepicker(datepickerOptions);
            $('.datepicker').each(function () {
                if (!$(this).parent().is('.input-group')) {
                    let groupId = "input-group-" + $(this).attr('id');
                    $(this).before('<div class="input-group date" id="' + groupId + '"><div class="input-group-addon"> <i class="fa fa-calendar"></i> </div></div>')
                    $('#' + groupId).append($(this));
                    $('#' + groupId).click( function () {
                        let theInput = $(this).find('input');
                        if (!theInput.datepicker("widget").is(":visible"))
                            theInput.focus();
                    });
                }
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
