@extends("errors.template")
@section("title",$title)
@section('head')
    <link href="/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .message {
            font-size: 18px;
            text-align: center;
            text-align: left;
        }

        body {
            padding: 0px;
        }
    </style>

@endsection
@section("message")
    <table>
        @foreach($tables as $table)
            @php($tableExists=Schema::hasTable($table))
            <tr>
                <td>
                    <i class="fa {{($tableExists?'fa-check-square-o text-success':'fa-exclamation-triangle text-danger')}}"></i>
                    <i class="fa fa-table "></i> {{$table}}
                </td>
                <td>
                    @if(!$tableExists)
                        <a href="{{route_('artisan',$table)}}" class="btn btn-default btn-xs">
                            <i class="fa fa-magic"></i>
                            Generate <strong class="text-primary">{{$table}}</strong> Table
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">
                <a href="{{route_('artisan','generate_all_tables')}}" class="btn btn-success btn-block">
                    <i class="fa fa-magic"></i>
                    Generate All Tables
                </a>
            </td>
        </tr>
    </table>
@endsection