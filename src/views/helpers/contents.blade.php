@section('head')
    <script type="text/javascript">
        $(function () {
        });
    </script>
@stop

@section('under-header')
    <ul class="breadcrumb">
        <li>
            <a href="/" class="text-warning" title="الرئيسة">
                <i class="fa fa-fw fa-home"></i>
                الرئيسة
            </a>
        </li>
        <li class="current" title="{!! $title !!}">{!! $title !!}</li>
    </ul>
    <div class="clear"></div>
@stop

@section('content')
    <div class="box box-primary book-pages">
        <div class="box-header book-header">
            <h3 class="box-title"><i class="fa fa-file-text-o"></i> {!! $title !!}
            </h3>
        </div>


        <div class="box-body">
            <div id="txt-read">
                {!! $txt !!}
            </div>
        </div>
    </div>
@stop