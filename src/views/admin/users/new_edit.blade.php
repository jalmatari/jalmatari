<?php
$userId = $rows['id']['data'];
$groups = Funs::UserGroups($userId)->pluck('id')->toArray();
$groups = Funs::GetUserGroups($groups);
?>
<script>
    $(function () {

        $('.job_title-form-row').after($('.group-form-row'));
        $("#permissionsAllCheck").on('ifChecked', function () {
            $(".permission_checker").iCheck("check");
        });

        $("#permissionsAllCheck").on('ifUnchecked', function () {
            $(".permission_checker").iCheck("uncheck");
        });
        @if(basename(Request::url('/'))!="add")
            $('label[for=password]').after('<sub class="text-green"> * تنويه: لا يتم تبطاقة كلمة المرور إلا إذا تم تعبئة الحقل التالي ...</sub>');
        @endif
        $('#username').before('<input style="display:none" type="text" name="username"/>');
        $('#password').before('<input style="display:none" type="text" name="password"/>');
        $('#password').attr({type: "password", autocomplete: "off"});
        $('#password').val('');
        $('#salary_method').change(function () {
            if ($(this).val() == 1000)
                $('.salary-form-row').hide();
            else
                $('.salary-form-row').show();
        });
        $('#salary_method').change();

        $('#salary').keyup(function () {
            if ($(this).val() == "" || $(this).val() < 0)
                $(this).val(0);
        });
        $('#salary').keyup();
        $('#permissions').after('<div class="clearfix"></div>' + hint('الصلاحيات المحددة، وغير القابلة للتحديد، هي الموروثة من الوظيفة'));
        $('#permissions').removeAttr('name');
        $('#permissions').after(function () {
            <?php
            $user_permissions = $rows['permissions']['data'];
            $job_title = key($rows['job_title']['data']);
            $job_permissions = \Jalmatari\Models\permissions::where('id', $job_title)->first();
            $permissions = (array) json_decode($job_permissions->permissions);
            $special_permissions = (array) json_decode($job_permissions->special_permissions);
            $permissions = array_merge($permissions, $special_permissions);
            ?>


                    return '{!!Funs::GetHtmlPermissionsForUser('permissions',$user_permissions,$permissions)!!}';
        });
        $('#job_title').change(function () {
            var permissions = $('input[name^="permissions["]:checked');
            var permissionsArr = [];
            $.each(permissions, function () {
                permissionsArr.push($(this).val());
            });
            J.api({
                ac: "permissions",
                job_id: $(this).val(),
                selected_permissions: permissionsArr
            }, job_title_changed,"{{ route_('admin.users.api') }}");
        });
        $('.must_in_project-form-row').after(hint('في حالة كانت القيمة=صفر في الخانة "المهام المطلوبة شهريا"ً، أو"المهام المطلوبة طيلة مدة المشروع"، فسيتم إعتماد القيمة المسندة للمسمى الوظيفي.'));
    });


    function job_title_changed(data) {
        $('.user_permissions_fileds').html(data);
    }
</script>
<style>
    .chosen-container {
        width: 100% !important;
    }
</style>
<div class="hidden">
    <div class="form-group group-form-row col-md-6">
        <label for="groups[]">المجموعات:</label>
        {!! Funs::Form('select',['groups[]',Funs::IsIn($groups,'data',$groups),Funs::IsIn($groups,'selected',null),["multiple"=>"multiple",'class'=>'form-control']]) !!}

    </div>
</div>