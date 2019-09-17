@section('body')
    <style>
        .form-group {
            height: 0;
            min-height: 20px;
        }

        .multi-field:first-child .remove-field {
            display: block;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        روابط جدول "{{$table->title==''?$table->name:$table->title}}"
                        <span class="text-green text-bold">{!! $saved?'محفوظ مسبقاً':'' !!}</span>
                    </h3>
                </div>
                {!!Funs::Form('open',[['route' =>['admin.tables.route',$table->id],'role'=>"form"]])!!}
                <div class=" col-md-12  margin">
                    <div class="form-group col-md-1">
                        <label>نوع الرابط</label>
                    </div>
                    <div class="form-group col-md-3">
                        <label>الرابط</label>
                    </div>
                    <div class="form-group col-md-3">
                        <label>اسم الرابط</label>
                    </div>
                    <div class="form-group col-md-2">
                        <label>الأمر المرتبط بالرابط</label>
                    </div>
                    <div class="form-group col-md-3">
                        <label>يتطلب رقم تعريفي (id)</label>
                    </div>
                    <div class="col-md-12 route-sample text-muted" style="display: none;" dir="ltr">
                        <div class="pull-left route">
                            Route::get("admin/{{$table->name}}", [ "as" => "admin.{{$table->name}}", "uses"
                            =>
                            "{{ucfirst($table->name)}}Controller@index" ]);

                        </div>
                        <br>
                        <div class="pull-left url">
                            {{url("{$table->name}")}}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="box-body multi-field-wrapper">
                    <div class="multi-fields">
                        @foreach($routes as $route)
                            <div class="multi-field {{$loop->first?'not-sortable':''}} col-md-12">
                                <div class="form-group col-md-1">
                                    {!! Funs::Form('hidden',['id[]',Funs::IsIn($route,'id',null)]) !!}
                                    {!! Funs::Form('select',['route_type[]',['get','post'],$route->route_type,['class'=>'un-advanced-select form-control']]) !!}
                                </div>
                                <div class="form-group col-md-2">
                                    {!! Funs::Form('select',['route_url[]',$routeNames,$route->route_url,['class'=>'un-advanced-select form-control']]) !!}
                                </div>
                                <div class="form-group col-md-2">
                                    {!! Funs::Form('select',['route_name[]',$routeNames,$route->route_name,['class'=>'un-advanced-select form-control']]) !!}
                                </div>
                                <div class="form-group col-md-2">
                                    {!! Funs::Form('select',['route_action[]',$routeNames,$route->route_action,['class'=>'un-advanced-select form-control']]) !!}
                                </div>
                                <div class="form-group col-md-1">
                                    {!! Funs::Form('select',['route_id_par[]',['','id'],Funs::IsIn($route,'route_id_par',0),['class'=>'un-advanced-select form-control']]) !!}
                                </div>
                                <div class="form-group col-md-4">

                                    <a href="#" class="btn btn-danger remove-field pull-left">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                    <a href="#" class="show-route btn btn-info pull-left">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                                <div class="col-md-12 route-sample text-muted" style="display: none;" dir="ltr">
                                    <div class="pull-left route">
                                        Route::get("admin/{{$table->name}}", [ "as" => "admin.{{$table->name}}", "uses"
                                        =>
                                        "{{ucfirst($table->name)}}Controller@index" ]);

                                    </div>
                                    <br>
                                    <div class="pull-left url">
                                        {{url("{$table->name}")}}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <a href="#" class="btn btn-info add-field"><i class="fa fa-plus-circle"></i> إضافة رابط
                        جديد</a>
                </div>
                <div class="box-footer">
                    <div class="pull-left">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> حفظ</button>
                    </div>
                </div>
                {!!Funs::Form('close')!!}
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.multi-field-wrapper').each(function () {
                var $wrapper = $('.multi-fields', this);
                $(".add-field", $(this)).click(function (e) {
                    $('.multi-field:first-child', $wrapper)
                        .clone(true)
                        .removeClass('not-sortable')
                        .removeClass('first-default')
                        .appendTo($wrapper)
                        .find('[name="id[]"]').val(null);
                    initAfterAdded();
                    return false;
                });
                $('.multi-field .remove-field', $wrapper).click(function () {
                    if ($('.multi-field', $wrapper).length >= 1)
                        $(this).closest('.multi-field').remove();
                    return false;
                });
            });
            initAfterAdded();

        });
        function initAfterAdded() {
            setIcheck();
            var rows = $('.multi-field');
            $.each(rows, function () {
                var thisSelect = $(this).find('select');
                thisSelect.change(function () {
                    multiFiled = $(this).closest('.multi-field');
                    isChecked = multiFiled.find('[name="route_id_par[]"]').val() == 1;
                    multiFiled.find('.route')
                        .html('Route::'
                            + multiFiled.find('[value="' + multiFiled.find('[name="route_type[]"]').val() + '"]').html()
                            + '("admin/{{$table->name}}/'
                            + multiFiled.find('[name="route_url[]"]').val()
                            + (isChecked ? '/{id}' : '')
                            + '", [ "as" => "admin.{{$table->name}}.'
                            + multiFiled.find('[name="route_name[]"]').val()
                            + '", "uses" => "{{ucfirst($table->name)}}Controller@'
                            + multiFiled.find('[name="route_action[]"]').val()
                            + '" ])'
                            + (isChecked ? '->where("id", "\\d+")' : '')
                            + ';');

                    multiFiled.find('.url')
                        .html('{{url("{$table->name}")}}/'
                            + multiFiled.find('[name="route_url[]"]').val()
                            + (isChecked ? '/id' : '')
                        );
                });

                thisSelect.change();
                $('.show-route').click(function () {
                    $(this).closest('.multi-field').find('.route-sample').toggle();
                    return false;
                });

            });
        }
    </script>
@stop