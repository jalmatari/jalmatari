@extends('admin.layouts.template')
@section('buttons')
    <li>
        <a href="{!!route_('admin.settings.add')!!}">
            <i class="icos-add"></i>
            <span>جديد</span>
            <strong></strong>
        </a>
    </li>

@stop

@section('body')

    @if(isset($sub_view))
        @if(View::exists($sub_view))
            @include($sub_view)
        @endif
    @endif
    <?php
    if (!isset($section))
        $section = $settings->first()->section;
    $setting_count = $settings->where('section', $section)->where('status', 1)->count();
    $per_page = 10;
    $tabs_count = ceil($setting_count / $per_page);
    $editores = [];

    ?>
    <div class="col-md-8">
        {!!Form::open(['route' =>'admin.settings.edit'])!!}
        <div class="nav-tabs-custom ">
            <ul class="nav nav-tabs">
                @for($i=1;$i<= $tabs_count;$i++)
                    <li {!! $i==1?'class="active"':'' !!}>
                        <a href="#tab{{$i}}" data-toggle="tab">@lang('Settings') {{$i}}</a>
                    </li>
                @endfor
            </ul>
            <div class="tab-content">
                <div id="tab1" class="tab-pane active">
                    <?php
                    $conter = 0;
                    $json_i = 0;
                    foreach ($settings as $row):
                    $class_hidden = "";
                    $isInSection = ($section == $row->section && $row->status == 1);
                    if (!$isInSection) {
                        $class_hidden = 'hidden';
                    } else {
                        $conter++;
                    }
                    if ($row['name'] == 'settings_names') {

                        continue;
                    } elseif (($conter > $per_page && $conter % $per_page == 1) && $isInSection) {
                        echo '</div><div id="tab' . ceil($conter / $per_page) . '" class="tab-pane">';
                    }
                    ?>
                    <div class="form-group {{'form_row_'.$row['name'].' '.$class_hidden}}">

                        @if ($row['type'] != "hidden")
                            {!! Form::label($row['name'],__($row['desc']).":") !!}
                        @endif
                        @if ($row['type'] == "checkbox")
                            {!! Form::{$row['type']}($row['name'],"",($row['value']==1)) !!}
                        @elseif ($row['type'] == "article")
                            {!! Form::select($row['name'],\App\Jalmatari\Models\contents::WhereCat(setting('settings-articles-cat')??1)->pluck('title','id'),$row['value'],['id'=>$row['name'],'class'=>"form-control",'style'=>"width:100%;"]) !!}
                            <a href="javascript:editItem('{!! route_("admin.contents.edit",':id')!!}','{!!$row['name']!!}')"
                               data-toggle="tooltip"
                               class="btn btn-info pull-left btn-xs" data-original-title="تحرير"
                               style="margin-right: 10px;">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>

                        @elseif ($row['type'] == "multi")
                            <?php
                            $multiArr = json_decode($row['value'], JSON_UNESCAPED_UNICODE);
                            if (!is_array($multiArr) || count($multiArr) == 0)
                                $multiArr = [ '' ];
                            ?>
                            <br>
                            <div class="multi-field-wrapper row-{{$row['name']}}">
                                <ol class="multi-fields niceList sortable" start="0">
                                    @foreach($multiArr as $row2)
                                        <li class="multi-field {{($json_i++<=2)?'not-sortable':''}}">
                                            {!! Form::text($row['name']."[]",$row2,["class"=>"filed"]) !!}
                                            <a href="#" class="btn btn-danger remove-field"><i class="fa fa-remove"></i></a>

                                            <i class="fa fa-arrows pull-left"></i>

                                        </li>
                                    @endforeach
                                </ol>
                                <a href="#" class="btn btn-info add-field"><i class="fa fa-plus-circle"></i> إضافة حقل
                                    جديد</a>
                            </div>
                        @elseif ($row['type'] == "list")
                            {!! Funs::Form('select',[$row['name'],json_decode($row['value']),null,["class"=>"form-control"]]) !!}
                        @elseif ($row['type'] == "multi_list")
                            <?php $multiList_data = Funs::GetMultiListData($row['name'], $row['value']);?>
                            <div class="clearfix"></div>
                            {!! Funs::Form('select',[$row['name'].'[]',$multiList_data['data'],$multiList_data['selected'],["class"=>"form-control","multiple"=>"multiple"]]) !!}
                        @elseif ($row['type'] == "editor")
                            <?php $editores[] = $row['name']; ?>
                            {!! Funs::Form('textarea',[$row['name'],$row['value'],["class"=>"form-control editor"]]) !!}
                        @elseif ($row['type'] == "image")
                            {!! Funs::Form('text',[$row['name'],$row['value'],["class"=>"form-control img-upload ltr"]]) !!}
                        @else
                            {!! Funs::Form($row['type'],[$row['name'],$row['value'],["class"=>"form-control"]]) !!}
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <?php endforeach; ?>
                </div>

                <script language="javascript">
                    function editItem(the_url, id) {
                        let e = document.getElementById(id);
                        let str = e.options[e.selectedIndex].value;
                        let win = window.open(the_url.replace(':id',str), '_blank');
                        win.focus();
                    }

                </script>
                <br>
                <div class="box-footer">
                    {!! Form::submit('حفظ',["class"=>"btn btn-primary pull-left"]) !!}
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>

@stop

@section('end')
    @if(count($editores)>=1)
        <script src='{{url('/')}}/jalmatari/plugins/ckeditor/ckeditor.js'></script>

        <script>
            $(function () {
                @foreach($editores as $editor)
                if ($('#{{$editor}}').length) {
                    CKEDITOR.replace('{{$editor}}', {
                        language: 'ar'
                    });
                }
                $('#{{$editor}}').after(function () {
                    return '<a id="img-btn"  class="btn btn-primary"> إضافة صورة</a>';
                });
                @endforeach
            });
        </script>
    @endif
    <script>
        $(function () {
            $('.multi-field-wrapper').each(function () {
                var $wrapper = $('.multi-fields', this);
                $(".add-field", $(this)).click(function (e) {
                    $('.multi-field:first-child', $wrapper).clone(true).removeClass('not-sortable').removeClass('first-default').appendTo($wrapper).find('input').val('').focus();
                    return false;
                });
                $('.multi-field .remove-field', $wrapper).click(function () {
                    if ($('.multi-field', $wrapper).length > 1)
                        $(this).parent('.multi-field').remove();
                    return false;
                });
            });
            $('.add-field').before('<sub class="text-red">* تنويه: يتم تخزين الأرقام فقط في قاعدة البيانات، وربطها مع هذه النصوص ، لذا تغيير الترتيب قد يأثر على البيانات المُدخلة سابقاً..</sub><br><br>');
            $(".sortable").sortable({
                items: "li:not(.not-sortable)"
            });
            $(".sortable").disableSelection();

        });
    </script>
    <style>
        .form-group {
            min-height: 0px;
        }

        .chosen-container {
            width: 100% !important;
        }
    </style>
@stop
