<?php
$group_id = $rows['id']['data'];
?>

<script>
    $(function () {
        $('.status-form-row').after($('.users-form-row'));
    });

</script>

<style>
    .users_fileds .users-list > li {
        height: 100px;
        border: 1px dashed #ddd;
        border-radius: 5px;
        margin: 5px;
        padding: 5px 0px;
        overflow-y: auto;
        width: 24%;
        min-width: 150px;
    }

    .user-chk {
        display: inline-block;
        float: right;
    }


    .users_fileds .users-list > li .text-light-blue {
        color: #605ca8 !important;
        margin: 0px;
    }

    .sections_start_numbers_rows label {
        width: 100px;
        text-align: left;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        float: right;
    }

    .users-list-name {
        text-align: right;
        float: right;
    }
    .user-job{
        float: right;
        top:.5em;
    }
</style>



<div class="hidden">
    <div class="form-group users-form-row col-md-12">
        {!! Funs::HtmlGroupUsersToCheck($group_id) !!}
    </div>
</div>