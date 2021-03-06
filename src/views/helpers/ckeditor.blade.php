<div id="fileManagerPanel"></div>
<link rel="stylesheet" href="{{asset('jalmatari/plugins/jalmatari/css/elfinder.min.css')}}">
<link rel="stylesheet" href="{{asset('jalmatari/plugins/jalmatari/css/theme.css')}}">
<link href="{{asset('jalmatari/plugins/jQueryUI/jquery-ui.min.css')}}" rel="stylesheet">


<script src="{{asset('jalmatari/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script src="{{asset('jalmatari/plugins/jQueryUI/jquery-ui.min.js')}}" type="text/javascript"></script>

<script type="text/javascript" src="{{asset('jalmatari/plugins/jalmatari/js/elfinder.min.js')}}"></script>
<script type="text/javascript" src="{{asset('jalmatari/plugins/jalmatari/js/i18n/elfinder.ar.js')}}"></script>
<script>
    $(function () {
        $('#fileManagerPanel').elfinder({
            lang: 'ar',             // language (OPTIONAL)
            url: '{{route_('jalmatari.elfinder.connector')}}' + (typeof fileManagerUrl == "undefined" ? '' : '?url=' + fileManagerUrl),
            width: '100%',
            height: '100%',
            dateFormat: 'Y-m-d',
            ui:['toolbar', 'stat'],
            uiOptions: {
                // toolbar configuration
                toolbar: [
                    ['back', 'forward', 'up'],
                    ['mkdir',  'upload']
                ]
            },
            resizable: false,
            {!! 'customData: '.  json_encode(['_token' => csrf_token()]).',' !!}
            getFileCallback: function (file, fm) {
                window.opener.CKEDITOR.tools.callFunction((function () {
                    var reParam = new RegExp('(?:[\?&]|&amp;)CKEditorFuncNum=([^&]+)', 'i');
                    var match = window.location.search.match(reParam);
                    return (match && match.length > 1) ? match[1] : '';
                })(), fm.convAbsUrl(file.url));
                fm.destroy();
                window.close();

            }
        });

    });
</script>
