var J = new function () {
    this.digits = function (num, n) {
        isMinus = num < 0;
        if (isMinus)
            num *= -1;
        digit = '';
        if (typeof n == 'undefined')
            n = 2;//two digits
        for (i = 1; i < n; i++) {
            if (parseInt(num) < (1 + Array(i + 1).join("0")))
                digit += '0';
        }
        digit = (isMinus ? '-' : '') + digit + num;
        return digit;
    }
    this.json = function (str) {
        return JSON.parse(str);
    }
    this.ar = function (num) {
        arabic_map = new Array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        en_map = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        r = num + '';
        arr = r.split('');
        for (i = 0; i < arr.length; i++)
            arr[i] = (en_map.indexOf(arr[i]) != -1) ? arabic_map[arr[i]] : arr[i] = arr[i];

        return arr.join('');
    }
    this.position = function (element) {
        var top = 0, left = 0;
        do {
            top += element.offsetTop || 0;
            left += element.offsetLeft || 0;
            element = element.offsetParent;
        } while (element);

        return {
            top: top,
            left: left
        };
    }
    this.api = function (dataToPass, fun_to_call, url, type) {
        if (typeof type === 'undefined')
            type = 'post';
        if (typeof url === 'undefined')
            url = '/admin/api';
        var data1 = {_token: _globalObj._token};
        $.extend(data1, dataToPass);
        var errorFun;
        if (!_.isUndefined(fun_to_call)) {
            if (!_.isFunction(fun_to_call) && _.isFunction(window[fun_to_call]))
                fun_to_call = window[fun_to_call];
            if (_.isFunction(fun_to_call))
                errorFun = fun_to_call.name + 'Error';
            else
                errorFun = fun_to_call + 'Error';

            if (_.isFunction(window[errorFun]))
                errorFun = window[errorFun];
        }

        $.ajax({
            dataType: "json",
            type: type,
            async: true,
            url: url,
            data: data1,
            success: function (data) {
                if (_.isFunction(fun_to_call))
                    fun_to_call(data);
            },
            error: function (xhr, status, error) {
                if (_.isFunction(errorFun))
                    errorFun(xhr, status, error);
            }

        });
    }
    this.dd = function () {
        for (i = 0; i < arguments.length; i++)
            console.log(arguments[i]);
    }
    this.random = function (min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min)) + min;

    }

    this.modal = function (title, body, size, modalClass, footerBtnClass, footerBtn) {
        return this.showModal(title, body, '#main-modal', size,false, modalClass, footerBtnClass, footerBtn);
    }

    this.showModal = function (title, body, mainId, size,showTheModal, modalClass, footerBtnClass, footerBtn) {
        var theClass = _.isUndefined(mainId) ? '#main-modal' : mainId;
        if (!$(theClass).length)
            this.addModal(theClass);
        if (_.isUndefined(size))
            size = 'lg';
        if (_.isUndefined(modalClass))
            modalClass = 'default';
        if (_.isUndefined(footerBtn))
            footerBtn = '';
        if (_.isUndefined(footerBtnClass))
            footerBtnClass = 'default';

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
        $(theClass + ' .modal-body').html('').append(body);

        $(theClass + ' .modal-footer .btn:not("[data-dismiss=modal]")').remove();
        $(theClass + ' .modal-footer .btn:not(:last-child)').remove();
        $(theClass + ' .modal-footer .btn')
            .removeClass('btn-default')
            .removeClass('btn-outline')
            .removeClass('btn-primary')
            .removeClass('btn-info')
            .removeClass('btn-warning')
            .removeClass('btn-success')
            .removeClass('btn-danger')
            .addClass('btn-' + footerBtnClass)
            .before(footerBtn);
        $(theClass).modal();
        if(_.isUndefined(showTheModal)||showTheModal) {
            $(theClass).modal('show');
            var modals=$('.modal');
            modalLen=modals.length-1;
            if(modalLen>=1) {
                var modalsBg = $('.modal-backdrop');

                modalzIndex=parseInt($(modals[modalLen - 1]).css('z-index'));

                $(modalsBg[modalLen]).css('z-index',modalzIndex+1);
                $(modals[modalLen]).css('z-index',modalzIndex+2);
            }
        }

        return theClass;
    }

    this.addModal = function (divId) {
        divId = _.isUndefined(divId) ? 'main-modal' : divId;
        divId = divId.replace('#', '');
        $('body').append(
            '<div id="' + divId + '" class="modal">\
            <div class="modal-dialog  modal-lg">\
            <div class="modal-content">\
            <div class="modal-header">\
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
            <span>×</span>\
            </button>\
        <h4 class="modal-title"></h4>\
            </div>\
            <div class="modal-body"></div>\
            <div class="modal-footer">\
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">إغلاق</button>\
            </div>\
            </div>\
        </div>\
        </div>'
        );
    }

}