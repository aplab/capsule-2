/**
 * Created by polyanin on 19.01.2017.
 */
function CapsuleCmsImageUploader()
{
    if (CapsuleCmsImageUploader.hasOwnProperty('instance')) {
        var m = 'Instance already exists. Only one instance allowed!';
        console.log(m);
        throw new Error(m);
    } else {
        CapsuleCmsImageUploader.instance = this;
    }

    /**
     * Class prefix
     *
     * @type {string}
     */
    var prefix = 'capsule-cms-image-uploader-';

    /**
     * Button wrapper class
     *
     * @see CapsuleCmsDialog
     * @type {string}
     */
    var btn_wrapper_class = 'capsule-cms-dialog-footer-button-3';

    /**
     * var CapsuleCmsDialog
     */
    var dialog_window;

    /**
     * file input
     */
    var file_input;

    /**
     * Selected files list
     */
    var list;

    /**
     * Create element wrapper
     *
     * @param tag
     * @returns {*|jQuery|HTMLElement}
     */
    var ce = function (tag)
    {
        tag = tag || 'div';
        return $(document.createElement(tag));
    };

    /**
     * Create dialog window
     */
    var create_window = function ()
    {
        dialog_window = CapsuleCmsDialog.createElement(
            'capsule-cms-image-uploader',
            {
                maximxze: 1,
                title: 'Upload images'
            }
        );
        var footer = dialog_window.getFooter();

        var btn_wrapper_1 = ce();
        btn_wrapper_1.addClass(btn_wrapper_class);
        footer.append(btn_wrapper_1);

        var btn_wrapper_2 = ce();
        btn_wrapper_2.addClass(btn_wrapper_class);
        footer.append(btn_wrapper_2);

        var btn_wrapper_3 = ce();
        btn_wrapper_3.addClass(btn_wrapper_class);
        footer.append(btn_wrapper_3);

        var btn_cancel = ce('button');
        btn_cancel.prop({
            type: 'button'
        }).addClass('btn btn-default').text('Cancel');
        btn_wrapper_1.append(btn_cancel);

        var btn_upload = ce('button');
        btn_upload.prop({
            type: 'button'
        }).addClass('btn btn-warning').text('Upload');
        btn_wrapper_2.append(btn_upload);

        var btn_browse = ce('button');
        btn_browse.prop({
            type: 'button'
        }).addClass('btn btn-success').text('Browse');
        btn_wrapper_3.append(btn_browse);

        dialog_window.show();

        file_input = ce('input');
        file_input.prop({
            type: 'file',
            multiple: 'multiple'
        }).css({
            display: 'none'
        });
        $('body').append(file_input);

        file_input.change(function ()
        {
            handleSelected();
        });

        list = ce();
        list.addClass(prefix + 'list');

        dialog_window.getBody().append(list);

        btn_browse.click(function ()
        {
            file_input.click();
        });

        btn_cancel.click(function ()
        {
            CapsuleCmsImageUploader.getInstance().purgeWindow();
        });

        btn_upload.click(function ()
        {
            uploadFiles();
        });

        file_input.click();
    };

    /**
     * Show window
     *
     * @access public
     */
    this.showWindow = function ()
    {
        create_window();
    };

    /**
     * Destroy window
     *
     * @access public
     */
    this.purgeWindow = function ()
    {
        dialog_window.purge();
        file_input.remove();
    };

    /**
     * Onchange (file input)
     */
    var handleSelected = function ()
    {
        var o_input = file_input[0];
        var number = o_input.files.length;
        if (!number) {
            return;
        }
        list.empty();
        for (var i = 0; i < number; i++) {
            var file = o_input.files[i];
            var line = $('<div>');
            line.addClass('progress capsule-cms-image-uploader-item');

            var name = $('<div>');
            name.addClass('capsule-cms-image-uploader-name');
            name.text(file.name);
            line.append(name);

            var size = $('<div>');
            size.addClass('capsule-cms-image-uploader-size');
            size.text(file.size);
            line.append(size);

            var type = $('<div>');
            type.addClass('capsule-cms-image-uploader-type');
            type.text(file.type);
            line.append(type);

            var progress = $('<div>');
            progress.addClass('progress-bar progress-bar-success capsule-cms-image-uploader-progress');
            // progress.text('1%');
            line.append(progress);

            list.append(line);
        }
    };

    var process_running = 0;

    var uploadFiles = function ()
    {
        var o_input = file_input[0];
        var number = o_input.files.length;
        if (!number) {
            input.click();
            return;
        }
        for (var i = 0; i < number; i++) {
            uploadFile(i);
        }
        file_input.wrap('<form>').closest('form').get(0).reset();
        file_input.unwrap();
        btn_browse.hide();
        btn_clear.hide();
        btn_upload.hide();
    };

    var uploadFile = function (i)
    {
        var o_input = file_input[0];
        var line = list.children().eq(i);
        line.addClass('capsule-cms-file-uploader-progress');
        var file = o_input.files[i];
        var form_data = new FormData();

        form_data.append('cmd', 'uploadPhoto');
        form_data.append('file', file);

        process_running++;

        $.ajax({
            url: '/ajax/',
            data: form_data,
            type: 'POST',
            // THIS MUST BE DONE FOR FILE UPLOADING
            contentType: false,
            processData: false,
            dataType: 'json',
            xhr: function ()
            {
                // var myXhr = $.ajaxSetup().xhr();
                var myXhr = $.ajaxSettings.xhr();
                (myXhr.upload || myXhr).addEventListener(
                    'progress',
                    function (e)
                    {
                        if (e.lengthComputable) {
                            var max = e.total;
                            var current = e.loaded;

                            var Percentage = (current * 100) / max;
                            var progressbar = list.find('.capsule-cms-image-uploader-progress').eq(i);
                            progressbar.css({
                                width: Percentage + '%'
                            });

                            if (Percentage >= 100) {
                                // process completed
                            }
                        }
                    },
                    false
                );
                return myXhr;
            },
            cache: false,
            success: function (data)
            {
                var current_line = line;
                if (data.status === 'ok') {
                    current_line.removeClass('capsule-cms-file-uploader-progress');
                    current_line.addClass('capsule-cms-file-uploader-success');
                } else {
                    current_line.removeClass('capsule-cms-file-uploader-progress');
                    current_line.addClass('capsule-cms-file-uploader-fail');
                    current_line.attr('title', data.message);
                }
                process_running--;
                if (process_running < 1) {
                    button_done.show();
                }
            },
            error: function (xhr, status, error)
            {
                var current_line = line;
                current_line.removeClass('capsule-cms-file-uploader-progress');
                current_line.addClass('capsule-cms-file-uploader-fail');
                process_running--;
                if (process_running < 1) {
                    button_done.show();
                }
            }
        });
    };
}

/**
 * Static method
 */
CapsuleCmsImageUploader.getInstance = function ()
{
    if (CapsuleCmsImageUploader.hasOwnProperty('instance')) {
        return CapsuleCmsImageUploader.instance;
    }
    return new CapsuleCmsImageUploader();
};
