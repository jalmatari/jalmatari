@section('list-end')
    <script>
        $(function () {
            $('#table-dynamic').addClass('sortable ui-sortable');
            $(".sortable").sortable({
                items: "tbody tr:not(.not-sortable)",
                placeholder: "ui-state-highlight",
                start: function (event, ui) {
                    $('.ui-state-highlight').css('height', ui.item[0].style.height);
                    if ($('.ui-state-highlight').length == 1)
                        $('.ui-state-highlight').after('<tr class="ui-state-highlight hidden"></tr>')
                },
                stop: function (event, ui) {
                    $('.ui-state-highlight.hidden').remove();
                    let currEl = $(ui.item[0]);
                    var id = currEl.find('[name="id[]"]').val();
                    let rows = $('#table-dynamic tbody>tr');
                    let currIndex = rows.index(currEl);
                    let position = 'after';
                    let positionId = 0;
                    if (currIndex + 1 < rows.length)
                        positionId = $(rows[currIndex + 1]).find('[name="id[]"]').val();
                    else if (currIndex != 0) {//use row before this
                        positionId = $(rows[currIndex - 1]).find('[name="id[]"]').val();
                        position = 'before';
                    }
                    J.api({
                        ac: 'orderTables',
                        id: id,
                        position: position,
                        positionId: positionId
                    }, tableOrdered, "{{route_('admin.tables.api')}}");
                }
            });
            $(".sortable").disableSelection();
        });

        function tableOrdered(data) {
            oTable.draw();
        }
    </script>
    <style>
        .ui-sortable-helper {
            display: table;
        }
    </style>
@stop
