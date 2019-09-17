<script>

    {!! "var route_actions = ".json_encode($rows['action']['data']).";" !!}
    $(function () {
        $('.form-group.col-md-6').removeClass('col-md-6').addClass('col-md-3');
        $('.status-form-row').after('<div class="clearfix"></div><div class="route-sample" dir="ltr"></div><br><br><br><div class="clearfix"></div>');
        $('select,[type="text"]').change(function () {
            getroute_();
        });
        $('input').on('ifChanged', function (event) {
            getroute_();
        });
        getroute_();
        $('#route').keyup(function () {
            getroute_();
        });
        $('#controller_id').change(function () {
            options = route_actions[$(this).val()];
            $('#action option').remove();
            $.each(options, function () {
                $('#action').append('<option value="' + this + '">' + this + '</option>');
            });

            $(' #action').trigger("chosen:updated");
        });

    });

    function getroute_() {
        api({
            ac: 'getRoute',
            route: $('#route').val(),
            controller_id: $('#controller_id').val(),
            action: $('#action').val(),
            middleware: $('#middleware').val(),
            type: $('#type').val(),
            id_required: $('#id_required[type="hidden"]').val(),
        }, 'writeroute_','{{route_('admin.routes.api')}}');
    }
    function writeroute_(data) {
        $('.route-sample').html(data);
    }
</script>

<style>
    [class*="form-row"] input{
        direction: ltr;
    }
</style>