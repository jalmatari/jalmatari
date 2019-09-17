<script>
    var files = 0;
    var cur_file = 0;
    var ids = [];

    function getCheckedBoxes() {
        var chkBoxs = $('#table-dynamic tbody [aria-checked="true"] [name="id[]"]');
        var chkBoxsVals = [];
        if (chkBoxs.length >= 1) {
            $.each(chkBoxs, function () {
                chkBoxsVals.push($(this).val());
            });
        } else {
            alert('يرجى تحديد الملفات قبل تنفيذ العملية!!');
        }
        return chkBoxsVals;
    }

    function changeSectionSelectedFiles() {
        var filesIds = getCheckedBoxes();
        if (filesIds.length >= 1) {
            var filesHtml = '';
            $.each(filesIds, function () {
                filesHtml += ' <div class="label label-default tag tag-file-id"> ' + this + '</div> ';
            });
            var body = '<div class="form-group">'
                    + '<label>الملفات المُراد نقلها:</label>'
                    + '<div class="clearfix"></div>'
                    + filesHtml
                    + '</div>'
                    + '<div class="form-group">'
                    + '<label>التصنيف المراد النقل إليه:</label>'
                    + '{!! Funs::Form("select",["",Funs::GetProjectSectionsList(0,0,1),0,["class"=>"form-control","id"=>"toSection"]]) !!}'
                    + '</div>'
                    + '<div class="clearfix"></div>';
            var btn_save_section = '<button type="button" class="btn pull-right btn-outline" id="cahnge-sections">نقل وإغلاق</button>';
            var theClass = showMyModal('نقل الأحاديث إلى تصنيف جديد', body, 'sm', 'primary', 'outline', btn_save_section);
            $('#cahnge-sections').click(function () {
                GetJson("{{route_('admin.home_tags.change_section')}}",{
                    filesIds: filesIds,
                    toSection: $('#toSection').val()
                }, 'sectionAdded');
                $(theClass).modal("hide");
                return false;
            });
            $(theClass).modal();
        }
    }

    function sectionAdded(data) {
        oTable._fnAjaxUpdate();
        $('#chk-all').iCheck('uncheck');
    }

    function showFilesInHome(parseAndShow) {

        if (typeof parseAndShow === 'undefined')
            parseAndShow = false;
        var filesIds = getCheckedBoxes();

        if (filesIds.length >= 1) {
            if (parseAndShow)
                parsingSelectedFiles();
            GetJson("{{route_('admin.tags_files.processing')}}", {ids: filesIds}, 'showFilesInHomeSuccess');
        }

        return false;
    }

    function showFilesInHomeSuccess(data) {
        var body = '<div class="text-center text-green">'
                + '<h1>تم!</h1>'
                + '<h3>جميع الملفات تم عرضها في الواجهة الرئيسية.</h3>'
                + '<h1><i class="fa fa-fw fa-check-circle-o"></i></h1>'
                + '</div>';
        var theClass = showMyModal('عرض الملفات في الواجهة', body);
        $(theClass).modal();
        oTable._fnAjaxUpdate();
    }


    function parsingSelectedFiles() {
        var filesIds = getCheckedBoxes();
        if (filesIds.length >= 1) {
            processing_php(filesIds);
        }
        return false;
    }

    function processing_php(filesIds) {
        if (typeof filesIds === 'undefined') {
            filesIds = ids;
        } else {
            ids = filesIds;
        }
        GetJson("{{route_('admin.files.processing')}}", {ids: filesIds}, 'processingPhpAjaxSuccess');
    }

    function processingPhpAjaxSuccess(data) {
        if (data != 0) {
            files = data;
            processing();
        } else {
            showMyModal('معالجة ملفات الوورد', '<div class="text-center text-green">'
                    + '<h1>تهانينا!</h1>'
                    + '<h3>جميع الملفات تم معالجتها.</h3>'
                    + '<h1><i class="fa fa-fw fa-check-circle-o"></i></h1>'
                    + '</div>');
            oTable._fnAjaxUpdate();
        }
    }

    function processing() {
        var can_prossing = cur_file < files.length;
        var modal_body = '';
        if (can_prossing) {
            var cur_progress = Math.floor((100 / files.length) * cur_file);

            var done_pross_txt = 'تمت معالجة ' + cur_file + ' من ' + files.length + ' (' + cur_progress + '%)';
            modal_body = '<div class="text-center">'
                    + '<h4>يرجى الإنتظار جاري معالجة الملفات...</h4>'
                    + '<h5>' + done_pross_txt + '</h5>'
                    + '<img src="/jalmatari/dist/img/128x128/Preloader_4/Preloader_4.gif" />'
                    + '</div>'
                    + '<div class="text-blue"> يجري حالياً معالجة الملف:<br> <div class="ltr pull-right text-red">' + files[cur_file].name + ' (' + files[cur_file].old_name + ')</div></div>'
                    + '<div class="clearfix"></div>'
                    + '<div class="progress active">'
                    + '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' + cur_progress + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + cur_progress + '%">'
                    + done_pross_txt
                    + '</div>'
                    + '</div>';
        } else {
            modal_body = '<div class="text-center text-green">'
                    + '<h4>تهانينا!</h4>'
                    + '<h5>تمت عملية المعالجة بنجاح.</h5>'
                    + '<h1><i class="fa fa-fw fa-check-circle-o"></i></h1>'
                    + '</div>';
            oTable._fnAjaxUpdate();
        }

        var theClass = showMyModal('معالجة ملفات الوورد', modal_body);
        $(theClass).modal();
        if (can_prossing) {
            parsingDocToHtml(files[cur_file].id);
            cur_file++;
        }
    }

    function parsingDocToHtml(file_id) {
        $('a[data-tag=' + file_id + ']').removeClass('btn-default').addClass('btn-info');
        GetJson("/admin/word2html/" + file_id, {kind: 'all', type: 'justParsing'}, 'parsingDocToHtmlSuccess');
    }

    function parsingDocToHtmlSuccess(data) {
        processing();

    }
</script>



















