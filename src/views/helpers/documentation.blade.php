@extends('admin.layouts.template',['showContentOnly'=>true])

@section('head')
    <style>
        body,table th{
        text-align: center;
        }
        table{
            text-align: right;
        }
        h3 {
            margin: 40px;
        }
        h4 {
            margin-top: 50px;
        }
    </style>
@endsection
@section('content')
    <table class="table table-bordered table-striped">
        <tr>
            <th>#</th>
            <th>اسم الجدول</th>
            <th>الوصف</th>
            <th>ملاحظات</th>
        </tr>
        @foreach($tables as $table)
            <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$table->name}}</td>
                <td>{{$table->title}}</td>
                <td>
                    @if(in_array($table->name,['errors','menu','tables','tables_cols','sync','controllers','routes']))
                        * مرتبط بالبكج الخاصة بجمال المطري (Jalmatari)
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <br><br>
    <h3>البنية التفصيلية لجداول قاعدة بيانات النظام</h3>

    @foreach($tables as $table)
        <h4>{{$table->name."({$table->title})" }}</h4>
        <table class="table table-bordered table-striped">
            <tr>
                <th width="20">#</th>
                <th width="150">اسم الحقل</th>
                <th width="200">نوع الحقل وحجمه</th>
                <th width="300">الوصف</th>
                <th>ملاحظات</th>
            </tr>
            @foreach($table->cols as $col)
                <tr>
                    <td>{{$loop->index+1}}</td>
                    <td dir="ltr" align="left">{{$col->COLUMN_NAME}}</td>
                    <td dir="ltr" align="left">{{$col->COLUMN_TYPE}}</td>
                    <td>{{$col->TITLE}}</td>
                    <td></td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endsection
