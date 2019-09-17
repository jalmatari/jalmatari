@section('head')
    <style>
        .user-list-table tr > td:not(:nth-child(2)) {
            text-align: center;
        }

    </style>
    <link rel="stylesheet" type="text/css" href="{{url('/jalmatari/plugins/jalmatari-datatables/datatables.min.css')}}"/>

@stop


@section('body')
    <div class="box box-primary box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">كشف المستخدمين ومشاركاتهم</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool hidden-print" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table  id="table-dynamic"  class="table table-bordered table-striped user-list-table" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th rowspan="2">م</th>
                    <th rowspan="2">اسم الموظف</th>
                    @foreach($types as $type)
                        <th colspan="2">{{$type}}</th>
                    @endforeach
                    <th class="bg-green" colspan="2">إجمالي المشاركات</th>
                </tr>
                <tr>
                    @for($i=0;$i<count($types);$i++)
                        <th class="text-green">م</th>
                        <th class="text-red">غ.م</th>
                    @endfor
                    <th class="bg-green">منشور</th>
                    <th class="bg-red">غ.منشور</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$counter++}}</td>
                        <td>
                            {{ $user->name }}<br>
                            <sub class="text-orange">({{$user->job->name}})</sub>
                        </td>
                        <?php $counts1 = 0; $counts2=0; ?>
                        @foreach($types as $type=>$name)
                            <?php                             $tdbrs = $user->{$type};

                            $count1 = $tdbrs->where('status',1)->count();
                            $count2=$tdbrs->where('status',0)->count();
                            $counts1+=$count1;
                            $counts2+=$count2;
                            ?>
                            <td class="text-green">{{$count1}}</td>
                            <td class="text-gray">{{$count2}}</td>
                        @endforeach
                        <td class="bg-green">{{$counts1}}</td>
                        <td class="bg-red">{{$counts2}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            <sup class="hint-txt text-green">* م: منشور.</sup><br><br>
            <sup class="hint-txt text-red">* غ.م: غير منشور.</sup>
        </div>
    </div>
@stop

@section('end')

    <script src="{{url('/jalmatari/plugins/jalmatari-datatables/datatables.min.js')}}"
            type="text/javascript"></script>
    <script type="text/javascript">
        var oTable;
        $(function () {

            var tableOptions = {
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "الجميع"]],
                "processing": true,
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
                }
            };

            oTable = $("#table-dynamic").DataTable(tableOptions);

        });
    </script>
@stop