/**
 * Created by jalmatari on 8/10/15.
 */


$(document).ready(function () {
    moment.locale('ar-sa');
    $('.moment').html(function () {
        return moment($(this).data('date'), "YYYY-MM-DD H:mm:ss").fromNow();
    });
    $('.moment-date').html(function () {
        return moment($(this).data('date'), "YYYY-MM-DD H:mm:ss").format("dddd YYYY/MM/DD");
    });
    $('#research-upload').after(function () {
        return '<a id="research-upload-btn" data-btn_id="research-upload" class="btn btn-primary"> تحديد الملف</a>';
    });
    if ($('#img-upload').val() == "")
        $('#img-upload').val('/jalmatari/img/users/default-user.png')
    $('#img-upload').attr('readonly', 'readonly');
    $('#img-upload').addClass('col-md-9');
    $('#img-upload').before('<a id="img-upload-btn" data-btn_id="img-upload" class="btn btn-primary btn-xs"> تحديد الصورة</a>');
    $('#img-upload').after('<div class="col-md-3 user-img-div"><img class="user-img" src="' + $('#img-upload').val() + '"></div>');
    $('#img-upload').before('<div class="clearfix"></div>');
    function fileManager(e) {

        $('<div id="fileManagerPanel" />').dialogelfinder({
            lang: 'ar',             // language (OPTIONAL)
            url: '/jalmatari/elfinder/connector'+ (typeof fileManagerUrl == "undefined" ? '' : '?url=' + fileManagerUrl),
            width: '80%',
            height: '600px',
            dateFormat: 'Y-m-d',
            customData:_globalObj,
            ui:['toolbar', 'stat'],
            uiOptions: {
                // toolbar configuration
                toolbar: [
                    ['back', 'forward'],
                    ['mkdir',  'upload']
                ]
            },
            getFileCallback: function (file) {
                var filePath = file; //file contains the relative url.
                $('#' + $(e.toElement).data('btn_id')).val(filePath.url)
                    .next('.user-img-div').find('img').attr('src', filePath.url);
                $('#fileManagerPanel').remove(); //close the window after image is selected
            }
        });
        return false;
    }


    $('#img-upload-btn').click(fileManager);

    $('select[multiple="multiple"]:not(.un-advanced-select,.no-select-all)').after('<button class="btn btn-xs btn-primary un-select-all"><i class="fa fa-fw fa-square-o"></i> إلغاء الجميع</button>' +
        ' <button class="btn btn-xs btn-success select-all"><i class="fa fa-fw fa-check-square-o"></i> اختيار الجميع</button>');
    $('.select-all').click(function () {
        $(this).parent().find('select option:not(:disabled)').prop('selected', true);
        $("select").trigger("chosen:updated");
        return false;
    });
    $('.un-select-all').click(function () {
        $(this).parent().find('select option:not(:disabled)').prop('selected', false);
        $("select").trigger("chosen:updated");
        return false;
    });
    addChosenSelect();

    $('.print-btn').click(function () {
        $('body').addClass('sidebar-collapse');
        window.print();
        return false;
    });
    setIcheck();
});

function GetJson(url, dataToPass, fun_to_call, type) {
    api(dataToPass, fun_to_call, url, type);
}

function api(dataToPass, fun_to_call, url, type) {
    if (typeof type === 'undefined')
        type = 'post';
    if (typeof url === 'undefined')
        url = '/admin/api';
    var data1 = {_token: _globalObj._token};
    $.extend(data1, dataToPass);
    $.ajax({
        dataType: "json",
        type: type,
        async: true,
        url: url,
        data: data1,
        success: function (data) {
            if (typeof fun_to_call != 'undefined' && typeof window[fun_to_call] === "function") {
                window[fun_to_call](data);
                $("select").trigger("chosen:updated");
            }
        }
    });
}

function addChosenSelect(selector) {
    if (typeof selector === 'undefined') {
        selector = '';
    }

    $(selector + " select.addable").on('chosen:ready',function(evt, params) {
        let choElement=params.chosen;
        let selElement=$(this);
        choElement.container.bind('keyup', function (e) {
            if (e.which === 13) {//If "Enter" Pressed
                let searchedTxt=choElement.search_field.val();
                let noResults=$(this).find(".chosen-results").children('li').hasClass('no-results');
                if(noResults&&searchedTxt.length>=1) {
                    selElement.append('<option value="'+searchedTxt+'" selected>* '+searchedTxt+'</option>');
                    selElement.trigger("chosen:updated");
                }
            }
        });
    });
    $(selector + " select:not(.un-advanced-select)").chosen("destroy");
    $(selector + ' .chosen-container').remove();
    $(selector + " select:not(.un-advanced-select)").addClass('chosen-rtl');
    $(selector + " select:not(.un-advanced-select)").chosen({
        no_results_text: "لا يوجد عنصر مطابق لـ:",
        placeholder_text: "إختر أحد العناصر",
        multiple_text: "أختر العناصر المطلوبة",
        search_contains: true
    });

}

function alert(msg) {
    notiy(msg, 'topLeft', 'warning');
}

function notiy(txt, position, styleType, animationOpen, animationClose) {

    if (typeof position === 'undefined') {
        position = 'topLeft';
    }
    if (typeof styleType === 'undefined') {
        styleType = 'information';
    }
    if (typeof animationOpen === 'undefined') {
        animationOpen = 'bounceInLeft';
    }
    if (typeof animationClose === 'undefined') {
        animationClose = 'bounceOutLeft';
    }
    noty({
        text: txt,
        layout: position,
        closeWith: ['click'],// ['click', 'button', 'hover', 'backdrop']
        type: styleType,
        theme: 'relax',
        animation: {
            open: 'animated ' + animationOpen,
            close: 'animated ' + animationClose,
            speed: 100
        },
        callback: {
            onClose: function () {
                $.noty.closeAll();
            }
        },
    });
}

function showMyModal(title, body, size, modalClass, footerBtnClass, footerBtn) {
    var theClass = '#main-modal';
    if (typeof size === 'undefined')
        size = 'lg';
    if (typeof modalClass === 'undefined')
        modalClass = 'default';
    if (typeof footerBtn === 'undefined')
        footerBtn = '';
    if (typeof footerBtnClass === 'undefined')
        footerBtnClass = 'primary';

    $(theClass + ' .modal-dialog')
        .removeClass('modal-lg')
        .removeClass('modal-sm')
        .addClass('modal-' + size);

    $(theClass + '').removeClass('modal-default')
        .removeClass('modal-primary')
        .removeClass('modal-info')
        .removeClass('modal-warning')
        .removeClass('modal-success')
        .removeClass('modal-danger')
        .addClass('modal-' + modalClass);

    $(theClass + ' .modal-title').html(title);
    $(theClass + ' .modal-body').html(body);

    $(theClass + ' .modal-footer .btn:not("[data-dismiss=modal]")').remove();

    $(theClass + ' .modal-footer .btn').removeClass('btn-default')
        .removeClass('btn-outline')
        .removeClass('btn-primary')
        .removeClass('btn-info')
        .removeClass('btn-warning')
        .removeClass('btn-success')
        .removeClass('btn-danger')
        .addClass('btn-' + footerBtnClass)
        .before(footerBtn);
    return theClass;
}

function hint(txt, color) {
    if (typeof color === 'undefined')
        color = "red";
    return '<br><sup class="hint-txt text-' + color + '">* تنويه: ' + txt + '</sup>';
}
function setIcheck() {
    $('input[type=checkbox]').iCheck('destroy');
    $('input:not(.un-icheck)[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green',
        increaseArea: '20%' // optional
    });

    $('.check-all-inputs').on('ifChecked', function () {
        $('.' + $(this).data('group-name')).iCheck("check");
    });

    $('.check-all-inputs').on('ifUnchecked', function () {
        $('.' + $(this).data('group-name')).iCheck("uncheck");
    });
}


function dd() {
    for (i = 0; i < arguments.length; i++)
        console.log(arguments[i]);
}

function leftPad(number, targetLength) {
    var output = number + '';
    while (output.length < targetLength) {
        output = '0' + output;
    }
    return output;
}