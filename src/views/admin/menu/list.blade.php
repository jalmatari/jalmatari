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
                stop: function (event, ui) {
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
@stop