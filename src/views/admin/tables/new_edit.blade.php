<script>
    $(function () {
        $('.box-footer').before('<section id="section-cols"><h4 class="page-header text-blue">حقول جدول "{{$table->title==''?$table->name:$table->title}}"</h4></setion>');
        $('#section-cols').append($('#cols'));

        $('[name="cols[SOURCE][]"]').change(function () {
            field = $(this).closest('.multi-field');
            if ($(this).val() == 'function')
                field.find('#fun-pars').show();
            else
                field.find('#fun-pars').hide();
        });
        $('.add-attr').click(function () {
            addAttr($(this).closest('.multi-field').data('column-name'));
            $('.remove-attr').click(function () {
                $(this).closest('.attr').remove();
                return false;
            });
            return false;
        });

        $('.remove-attr').click(function () {
            $(this).closest('.attr').remove();
            return false;
        });
        $('.multi-field-wrapper').each(function () {
            var $wrapper = $('.multi-fields', this);
            $(".add-field", $(this)).click(function (e) {
                $('.multi-field:first-child', $wrapper)
                    .clone(true)
                    .removeClass('not-sortable')
                    .removeClass('first-default')
                    .appendTo($wrapper)
                    .find('[name="id[]"]').val(null);
                return false;
            });
            $('.multi-field .remove-field', $wrapper).click(function () {
                if ($('.multi-field', $wrapper).length >= 1)
                    $(this).closest('.multi-field').remove();
                return false;
            });
        });
    });


    function addAttr(colName, key, val) {
        if (typeof key === 'undefined')
            key = '';
        if (typeof val === 'undefined')
            val = '';
        attrRow = '<div class="attr">'
            + '<div class="form-group col-xs-4">'
            + '<input class="form-control" dir="ltr" name="cols[attr_key_' + colName + '][]" type="text" value="' + key + '" placeholder="الخاصية">'
            + '</div>'
            + '<div class="form-group col-xs-7">'
            + '<input class="form-control" name="cols[attr_val_' + colName + '][]" type="text" value="' + val + '" placeholder="القيمة">'
            + '</div>'
            + '<div class="form-group col-xs-1">'
            + '<a href="#" class="bg-red remove-attr pull-left">'
            + '<i class="fa fa-remove"></i>'
            + '</a>'
            + '</div>'
            + '</div><div class="clearfix"></div> ';

        $('.' + colName + '_row .add-attr-div').before(attrRow);
    }
</script>

<div id="cols">
    <div class=" col-md-12  margin">
        <div class="form-group col-md-2">
            <label>اسم الحقل</label>
        </div>
        <div class="form-group col-md-2">
            <label>عنوان الحقل</label>
        </div>
        <div class="form-group col-md-1">
            <label>نوع الحقل</label>
        </div>
        <div class="form-group col-md-2">
            <label>مصدر بيانات الحقل عند التحرير</label>
        </div>
        <div class="form-group col-md-1">
            <label>عرض كعمود في جدول العرض </label>
        </div>
        <div class="form-group col-md-3">
            <label>خصائص الحقل عند التحرير (الخاصية - القيمة) </label>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="box-body multi-field-wrapper">
        <div class="multi-fields">
            @foreach($cols as $col)
                <div class="multi-field {{$col->COLUMN_NAME.'_row '.($loop->first?'not-sortable':'')}} col-md-12"
                     data-column-name="{{$col->COLUMN_NAME}}">
                    <div class="form-group col-md-2">
                        {!! Funs::Form('hidden',['cols[ID][]',Funs::IsIn($col,'ID',null)]) !!}
                        {!! Funs::Form('text',['cols[COLUMN_NAME][]',$col->COLUMN_NAME,['class'=>'form-control','dir'=>'ltr']]) !!}
                        {!! Funs::Form('hidden',['cols[OLD_COLUMN_NAME][]',$col->COLUMN_NAME]) !!}
                    </div>
                    <div class="form-group col-md-2">
                        {!! Funs::Form('text',['cols[TITLE][]',$col->TITLE,['class'=>'form-control']]) !!}
                    </div>
                    <div class="form-group col-md-1">
                        {!! Funs::Form('select',['cols[TYPE][]',Funs::$ColsTypes,$col->TYPE,['class'=>'un-advanced-select form-control']]) !!}
                    </div>
                    <div class="form-group col-md-2">
                        <?php
                        $source = $col->SOURCE;
                        $isFunction = false;

                        if ($source != '') {
                            $col->SOURCE = json_decode($col->SOURCE);
                            $isFunction = isset($col->SOURCE->function);
                            $source = $isFunction ? 'function' : '';
                            //dd($col->SOURCE);

                        }
                        echo Funs::Form('select', [ 'cols[SOURCE][]', [ null, 'function' => 'دالة' ], $source, [ 'class' => 'un-advanced-select form-control' ] ]);

                        ?>
                        <div id="fun-pars" {!! $isFunction?'':'style="display:none"' !!}>
                            {!! Funs::Form('label',[ 'cols[source_fun_name_'.$col->COLUMN_NAME.']','اسم الدالة:']) !!}
                            {!! Funs::Form('select',['cols[source_fun_name_'.$col->COLUMN_NAME.']',$sourceFuns,Funs::IsIn($col->SOURCE,'function',null),['class'=>'un-advanced-select form-control']]) !!}
                            </div>
                    </div>
                    <div class="form-group col-md-1">
                        {!! Funs::Form('checkbox',['cols[SHOW_IN_LIST_'.$col->COLUMN_NAME.']',null,$col->SHOW_IN_LIST]) !!}
                    </div>
                    <div class="form-group col-md-3 attrs">
                        @if ($col->ATTR != '')
                            <?php $attrs = json_decode($col->ATTR,true); ?>
                            @if (count($attrs) >= 1)
                                @foreach ($attrs as $key => $attr)

                                    <div class="attr">
                                        <div class="form-group col-xs-4">
                                            {!! Funs::Form('text',['cols[attr_key_'.$col->COLUMN_NAME.'][]',$key,['class'=>'form-control','dir'=>'ltr','placeholder'=>'الخاصية']]) !!}
                                        </div>
                                        <div class="form-group col-xs-7">
                                            {!! Funs::Form('text',['cols[attr_val_'.$col->COLUMN_NAME.'][]',$attr,['class'=>'form-control','placeholder'=>'القيمة']]) !!}
                                        </div>
                                        <div class="form-group col-xs-1">
                                            <a href="#" class="bg-red remove-attr pull-left">
                                                <i class="fa fa-remove"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                        <div class="clearfix"></div>
                        <div class="form-group col-xs-1 pull-left add-attr-div">
                            <a href="#" class="bg-green add-attr pull-left">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="form-group col-md-1">
                        <a href="#" class="btn btn-danger remove-field pull-left disabled">
                            <i class="fa fa-remove"></i>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            @endforeach
        </div>
        <div class="clearfix"></div>
        <br>

        <a href="#" class="btn btn-info add-field disabled"><i class="fa fa-plus-circle"></i> إضافة حقل
            جديد</a>
        <span class="hint text-aqua">*فكرة مستقبلية</span>
    </div>
</div>


<style>
    .form-group {
        min-height: 20px;
        margin: 0px;
        padding: 7px 2px;
    }

    .multi-field:first-child .remove-field {
        display: block;
    }

    textarea.form-control {
        height: 34px;
    }

    .add-attr, .remove-attr {
        padding: 3px;
        font-size: 13px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        text-align: center;
    }

    .remove-attr {
        padding: 2px;
        margin-top: 8px;
    }
</style>