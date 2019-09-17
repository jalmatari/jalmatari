<script>
    var fontAwesomeIconsCalled = false;
    var modalClass = '';
    $(function () {
        $('#icon').after(function () {
            return '<div class="icon-group input-group"><span class="input-group-addon"><i class="fa ' + $(this).val() + '"></i></span></div>';
        });
        $('.icon-group').append($('#icon')).append('<div class="input-group-btn">'
            + '<button type="button" class="btn btn-primary select-icon"><i class="fa fa-font-awesome"></i> إختيار أيقونة...</button>'
            + '</div>');

        $('.select-icon').click(function () {

            if (fontAwesomeIconsCalled)
                fontAwesomeIcons();
            else
                api({ac: 'fontAwesomeIcons'}, 'fontAwesomeIcons', '{{route_('admin.menu.api')}}');
            return false;
        });
    });

    function fontAwesomeIcons(data) {
        if (!fontAwesomeIconsCalled) {
            modalClass = showMyModal('إختيار أيقونة', data);
            $('.fontawesome-icon-list a').click(function () {
                $(modalClass).modal("hide");
                theIcon=$(this).find('.fa').attr('class').split(' ').pop();
                $('#icon').val(theIcon);
                $('.icon-group .input-group-addon .fa').attr('class','fa '+theIcon);
                return false;
            });
            fontAwesomeIconsCalled = true;
        }
        $(modalClass).modal();
    }
</script>
<style>
    #icon {
        border-radius: 0px !important;
    }

    .select-icon {
        direction: rtl !important;
    }
    #name,#icon{
        direction: ltr;
    }
    .chosen-container .chosen-results li {
        font-family: monospace;
    }
</style>