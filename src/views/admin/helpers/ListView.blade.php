@extends('admin.layouts.template')
@section('buttons')
    @if(View::exists('admin.'.$name.'.topBar'))
        @include('admin.'.$name.'.topBar')
    @endif
    @if(\Jalmatari\Models\tables::showAddBtn($name))
        <a href="{{route_("admin.{$name}.add")}}" class="btn btn-primary">
            <i class="fa fa-plus-square"></i>
            <span>{{\Jalmatari\Models\tables::addBtn($name)}}</span>
        </a>
    @endif

@stop

@section('head')

    <style>
        .box-header > .box-tools {
            display: flex;
        }

        .btn-group > .btn + .dropdown-toggle {
            padding: 6px;
        }

        .btn-group .btn + .btn {
            margin: 0px;
        }

        #table-dynamic .btn {
            margin: 0px;
            padding: 5px 3px;
            height: 25px;
            line-height: 18px;
        }
    </style>

    <link rel="stylesheet" type="text/css"
          href="{{url('/jalmatari/plugins/jalmatari-datatables/datatables.min.css')}}"/>
@stop

@section('body')

    <div class="btn-group pull-left margin-5 list-top-btns">
        @yield('buttons')
        @include('admin.helpers.topBar')
    </div>
    <div class="clearfix"></div>
    @if($other_view!="none")
        @include($other_view)
    @endif
    @if(View::exists('admin.'.$name.'.list'))
        @include('admin.'.$name.'.list')
    @endif
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ Funs::GetPageTitle($title) }}</h3>

            <div class="box-tools pull-right">


            </div>
        </div>
        <div class="box-body table-responsive">
            <table id="table-dynamic" class="table table-bordered table-striped" cellspacing="0" width="100%">
                <thead>
                <tr>
                    @foreach($cols as $row)
                        <th>{{ $row }}</th>
                    @endforeach
                </tr>
                </thead>
            </table>
        </div>
        <div class="clear"></div>
    </div>
@stop

@section('end')

    <script src="{{url('/jalmatari/plugins/jalmatari-datatables/datatables.min.js')}}"
            type="text/javascript"></script>
    @yield('list-end')
    <script type="text/javascript">
        var oTable;
        $(function () {

            var tableOptions = {
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "الجميع"]],
                "processing": true,
                "serverSide": true,
                "order": [[0, "desc"]],
                "bStateSave": true,
                "iStateDuration": 60 * 60 * 24 * 1000,
                "language": {
                    "sProcessing": "جاري التحميل...",
                    "sLengthMenu": "أظهر مُدخلات _MENU_",
                    "sZeroRecords": "لم يُعثر على أية سجلات",
                    "sInfo": "إظهار _START_ إلى _END_ من  _TOTAL_ ",
                    "sInfoEmpty": "يعرض 0 إلى 0 من  0 ",
                    "sInfoFiltered": "(منتقاة من مجموع _MAX_ )",
                    "sInfoPostFix": "",
                    "sSearch": "ابحث:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "الأول",
                        "sPrevious": "السابق",
                        "sNext": "التالي",
                        "sLast": "الأخير"
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    tableDrawed(oSettings);
                },
                "ajax": {
                    url: "",
                    type: 'POST',
                    data: function (d) {
                        d._token = _globalObj._token;
                    }
                }
            };
            if (typeof tableOptions2 !== 'undefined') {
                $.extend(tableOptions, tableOptions2);
            }
            oTable = $("#table-dynamic").DataTable(tableOptions);

            $('#table-dynamic').on('click', 'td .expand-details', function () {
                var nTr = $(this).closest('tr');
                if (oTable.fnIsOpen(nTr)) {
                    $(this).removeClass('fa-minus-square-o')
                        .removeClass('fa-plus-square')
                        .addClass('fa-plus-square');
                    oTable.fnClose(nTr);
                }
                else {
                    /* Open this row */
                    $(this).removeClass('fa-plus-square')
                        .removeClass('fa-minus-square-o')
                        .addClass('fa-minus-square-o');
                    oTable.fnOpen(nTr, fnFormatDetails(nTr), 'details');
                }
                return false;
            });

            $('#table-dynamic').on('click', '.btn.delete', function () {
                deleteItem($(this).attr('href'));
                return false;
            });

            $('#table-dynamic').on('click', '.btn.publish', function () {
                publishItem($(this).attr('href'));
                return false;
            });

            $('.action-to-multi').click(function () {
                checkboxes = getDataTableCheckedBoxes();
                if (checkboxes.length >= 1)
                    J.api({
                            type: $(this).data('type'),
                            ids: checkboxes,
                        }, actionToMultiDone, '{{Funs::ActionToMultiRoute()}}'
                    );
                return false;
            });


        });
            <?php
            $jsStr = '';
            if (session()->has('fun_tabledata_loaded'))
                $jsStr = 'var fun_name = "' . session()->pull('fun_tabledata_loaded', '') . '";
        if (typeof window[fun_name] === "function")
            window[fun_name]();';
            ?>
        var lastChecked = null;

        function tableDrawed(oSettings) {
            @yield('inside-datatable-loaded-function')
            {!! $jsStr !!}
            //datatable_loaded();
            /*setIcheck();
             $('#chk-all').on('ifChanged', function () {
             var checked = 'uncheck';
             if ($(this).is(':checked')) {
             checked = 'check';
             }
             $('input[name="id[]"]').iCheck(checked);
             });*/
            expandedTr();
            var row_bg_color = $('.row-bg-color');
            $.each(row_bg_color, function () {
                $(this).closest('tr').addClass($(this).val());
            });

            $('#datatable-check-all').on('ifChanged', function () {
                var checked = 'uncheck';
                if ($(this).is(':checked')) {
                    checked = 'check';
                }
                $(this).closest('table').find('input[name="id[]"]').iCheck(checked);
            }).closest('th')
                .attr('style', 'padding: 0px; padding-bottom: 10px;')
                .removeAttr('aria-sort')
                .removeAttr('aria-label')
                .removeAttr('class')
                .off();
            var chkboxes = $('#table-dynamic tbody input[name="id[]"]');
            chkboxes.click(function (e) {
                if (!lastChecked) {
                    lastChecked = this;
                    return;
                }

                if (e.shiftKey) {
                    var start = chkboxes.index(this);
                    var end = chkboxes.index(lastChecked);

                    chkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', lastChecked.checked);

                }

                lastChecked = this;
            });
        }

        function expandedTr() {

            $('#table-dynamic tr').click(function (e) {

                var origin = e.srcElement || e.target;
                var iconExpand = $(this).find('.expand-details');
                if (origin.tagName.toLowerCase() == "td" && iconExpand.length >= 1) {
                    if (oTable.fnIsOpen(this)) {
                        $(iconExpand).removeClass('fa-minus-square-o').removeClass('fa-plus-square').addClass('fa-plus-square');
                        oTable.fnClose(this);
                    }
                    else {
                        $(iconExpand).removeClass('fa-plus-square').removeClass('fa-minus-square-o').addClass('fa-minus-square-o');
                        oTable.fnOpen(this, fnFormatDetails(this), 'details');
                    }
                    return false;
                }
            });
        }

        function actionToMultiDone(data) {
            //dd(data);
            oTable.draw();

        }

        function publishItem(the_url) {
            GetJson(the_url, {}, 'rowPublish');
        }

        function rowPublish(data) {
            btn = $('#publish_' + data);
            if (btn.hasClass('btn-success'))
                btn.removeClass('btn-success')
                    .addClass('btn-warning')
                    .find('.fa-check')
                    .removeClass('fa-check')
                    .addClass('fa-times');
            else
                btn.removeClass('btn-warning')
                    .addClass('btn-success')
                    .find('.fa-times')
                    .removeClass('fa-times')
                    .addClass('fa-check');

        }

        function deleteItem(the_url, deletMessage, returnedId) {
            if (_.isUndefined(returnedId))
                returnedId = null;
            if (_.isUndefined(deletMessage))
                deletMessage = ' إنتبه: هل أنت متأكد أنك تريد الحذف؟';
            noty({
                text: deletMessage,
                layout: 'topCenter',
                theme: 'relax',
                animation: {
                    open: 'animated bounceInLeft',
                    close: 'animated bounceOutLeft',
                    speed: 100
                },
                buttons: [
                    {
                        addClass: 'btn btn-danger pull-left margin', text: 'إلغاء الأمر', onClick: function ($noty) {
                            $noty.close();
                        }
                    },
                    {
                        addClass: 'btn btn-primary pull-left margin', text: 'نعم', onClick: function ($noty) {
                            $noty.close();
                            GetJson(the_url, {returnedId: returnedId}, 'rowDeleted');
                        }
                    }
                ]
            });
        }

        function rowDeleted(data) {
            if (data >= 1) {
                $('#delete_' + data).closest('tr').fadeOut(300, function () {
                    $(this).remove();
                });
            } else {
                alert(data);
            }
        }

        function getDataTableCheckedBoxes() {
            //var chkBoxs = $('#table-dynamic tbody [aria-checked="true"] [name="id[]"]');
            var chkBoxs = $('#table-dynamic tbody input[name="id[]"]:checked');
            var chkBoxsVals = [];
            if (chkBoxs.length >= 1) {
                $.each(chkBoxs, function () {
                    chkBoxsVals.push($(this).val());
                });
            } else {
                alert('يرجى تحديد العناصر قبل تنفيذ العملية!!');
            }
            return chkBoxsVals;
        }

        /* Formating function for row details */
        function fnFormatDetails(nTr) {

            var aData = oTable.fnGetData(nTr);
            data1 = {_token: _globalObj._token};
            $.ajax({
                dataType: "json",
                type: "post",
                async: true,
                url: "/admin/tag-opened/" + aData[33],
                data: data1,
                success: function (data) {
                    $(nTr).removeClass('bg-gray');
                }
            });
            var sOut = aData[aData.length - 1];
            return sOut;
        }

        function datatable_loaded() {


        }
    </script>
@stop