@section('list-end')
    <script>
        var tableOptions2 = {

            columnDefs: [{
                targets: [7],
                orderData: [7, 2, 6]
            }, {
                targets: [2],
                orderData: [7, 2, 6]
            }, {
                targets: [6],
                orderData: [7, 2, 6]
            }]
        };
        $(function () {
            $('#table-dynamic').addClass('sortable ui-sortable');
            $(".sortable").sortable({
                items: "tbody tr:not(.not-sortable)",
                placeholder: "ui-state-highlight",
                start:function (event, ui) {
                    $('.ui-state-highlight').css('height',ui.item[0].style.height);
                    if($('.ui-state-highlight').length==1)
                        $('.ui-state-highlight').after('<tr class="ui-state-highlight hidden"></tr>')
                },
                stop: function (event, ui) {
                    $('.ui-state-highlight.hidden').remove();
                    var id = $(ui.item[0]).find('[name="id[]"]').val();//ui.item[0].childNodes[0].innerText.trim();
                    api({
                        ac: 'orderMenus',
                        id: id,
                        order_by: parseInt((ui.position.top - ui.originalPosition.top) / ui.item[0].clientHeight),
                    }, 'orderMenus', "{{route_('admin.menu.api')}}");
                }
            });
            $(".sortable").disableSelection();
        });
        function orderMenus(data) {
            oTable.draw();
        }
    </script>
    <style>
        .ui-sortable-helper{
            display: table;
        }
    </style>
@stop
