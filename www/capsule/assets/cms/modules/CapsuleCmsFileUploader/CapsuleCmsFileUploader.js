/**
 * Created by polyanin on 19.01.2017.
 */
function CapsuleCmsFileUploader()
{
    if (CapsuleCmsFileUploader.hasOwnProperty('instance')) {
        var m = 'Instance already exists. Only one instance allowed!';
        console.log(m);
        throw new Error(m);
    } else {
        CapsuleCmsFileUploader.instance = this;
    }

    var url = '/ajax/uploadImage/';

    /**
     * Class prefix
     *
     * @type {string}
     */
    var class_prefix = 'capsule-cms-file-uploader-';

    /**
     * Button wrapper class
     *
     * @see CapsuleCmsDialog
     * @type {string}
     */
    var button_wrapper_class_prefix = 'capsule-cms-dialog-footer-button-';

    /**
     * var CapsuleCmsDialog
     */
    var dialog_window;

    /**
     * Dialog window exists flag
     *
     * @type {boolean}
     */
    var dialog_window_exists = false;

    /**
     * file input
     */
    var file_input;

    /**
     * Selected files list
     */
    var list;

    /**
     * Button browse
     */
    var button_browse, button_browse_wrapper;

    /**
     * button upload
     */
    var button_upload, button_upload_wrapper;

    /**
     * button cancel
     */
    var button_cancel, button_cancel_wrapper;

    /**
     * button done
     */
    var button_done, button_done_wrapper;

    /**
     * button more
     */
    var button_more, button_more_wrapper;

    var button_group, button_group_done;

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
     * init file input
     *
     * @type {*}
     */
    var init_file_input = function ()
    {
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
    };

    /**
     * Clear file input
     */
    var clear_file_input = function ()
    {
        file_input.wrap('<form>').closest('form').get(0).reset();
        file_input.unwrap();
    };

    /**
     * Create dialog window
     */
    var create_window = function ()
    {
        dialog_window = CapsuleCmsDialog.createElement(
            'capsule-cms-file-uploader',
            {
                maximxze: 1,
                title: 'Upload files',
                width: 640
            }
        );
        var footer = dialog_window.getFooter();

        button_group = ce();
        button_group_done = ce();
        footer.append(button_group_done);
        footer.append(button_group);

        button_cancel_wrapper = ce();
        button_cancel_wrapper.addClass(button_wrapper_class_prefix + '3');
        button_group.append(button_cancel_wrapper);

        button_upload_wrapper = ce();
        button_upload_wrapper.addClass(button_wrapper_class_prefix + '3');
        button_group.append(button_upload_wrapper);

        button_browse_wrapper = ce();
        button_browse_wrapper.addClass(button_wrapper_class_prefix + '3');
        button_group.append(button_browse_wrapper);

        button_done_wrapper = ce();
        button_done_wrapper.addClass(button_wrapper_class_prefix + '2');
        button_group_done.append(button_done_wrapper);

        button_more_wrapper = ce();
        button_more_wrapper.addClass(button_wrapper_class_prefix + '2');
        button_group_done.append(button_more_wrapper);

        button_cancel = ce('button');
        button_cancel.prop({
            type: 'button'
        }).addClass('btn btn-default').text('Cancel');
        button_cancel_wrapper.append(button_cancel);

        button_upload = ce('button');
        button_upload.prop({
            type: 'button'
        }).addClass('btn btn-warning').text('Upload');
        button_upload_wrapper.append(button_upload);

        button_browse = ce('button');
        button_browse.prop({
            type: 'button'
        }).addClass('btn btn-success').text('Browse');
        button_browse_wrapper.append(button_browse);

        button_done = ce('button');
        button_done.prop({
            type: 'button'
        }).addClass('btn btn-success').text('Done');
        button_done_wrapper.append(button_done);

        button_more = ce('button');
        button_more.prop({
            type: 'button'
        }).addClass('btn btn-default').text('More');
        button_more_wrapper.append(button_more);

        list = ce();
        list.addClass(class_prefix + 'list');

        dialog_window.getBody().append(list);

        button_browse.click(function ()
        {
            file_input.click();
        });

        button_cancel.click(function ()
        {
            CapsuleCmsFileUploader.getInstance().purgeWindow();
        });

        button_done.click(function ()
        {
            if (process_running) {
                return;
            }
            CapsuleCmsFileUploader.getInstance().done();
        });

        button_more.click(function ()
        {
            if (process_running) {
                return;
            }
            CapsuleCmsFileUploader.getInstance().purgeWindow();
            CapsuleCmsFileUploader.getInstance().showWindow();
        });

        button_upload.click(function ()
        {
            uploadFiles();
        });

        button_group.show();
        button_group_done.hide();
        dialog_window_exists = true;
    };

    create_window();
    init_file_input();

    /**
     * Show window
     *
     * @access public
     */
    this.showWindow = function (param)
    {
        param = param || {};
        button_group.show();
        button_group_done.hide();
        list.empty();
        dialog_window.show();
        file_input.click();
    };

    /**
     * Destroy window
     *
     * @access public
     */
    this.purgeWindow = function ()
    {
        dialog_window.hide();
        clear_file_input();
    };

    /**
     * Set url
     *
     * @param value
     */
    this.setUrl = function (value)
    {
        url = value;
        return this;
    };

    /**
     * Set title
     *
     * @param title
     */
    this.setTitle = function(title)
    {
        dialog_window.setTitle(title);
    };

    /**
     * Done button handler
     */
    this.done = function ()
    {
        CapsuleCmsFileUploader.getInstance().purgeWindow();
        CapsuleCmsImageHistory.getInstance().showWindow();
    };

    /**
     * Visuslisation objects
     *
     * @type {Array}
     */
    var items = [];

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
        items = [];
        for (var i = 0; i < number; i++) {
            var file = o_input.files[i];
            var item = ce();
            items[i] = {
                item: item
            };
            item.addClass(class_prefix + 'item');
            var metadata = ce();
            metadata.addClass(class_prefix + 'metadata');
            item.append(metadata);
            items[i].metadata = metadata;

            var name = ce();
            name.addClass(class_prefix + 'name');
            name.text(file.name);
            metadata.append(name);
            items[i].name = name;

            var size = ce();
            size.addClass(class_prefix + 'size');
            size.text(file.size);
            metadata.append(size);
            items[i].size = size;

            var type = ce();
            type.addClass(class_prefix + 'type');
            type.text(file.type);
            metadata.append(type);
            items[i].type = type;

            var progress = ce();
            progress.addClass('progress ' + class_prefix + 'progress');
            item.append(progress);
            items[i].progress = progress;

            var progress_bar = ce();
            progress_bar.addClass(class_prefix + 'progress-bar progress-bar progress-bar-info progress-bar-striped active');
            progress.append(progress_bar);
            items[i].progress_bar = progress_bar;

            var status = ce();
            status.addClass(class_prefix + 'status');
            item.append(status);
            status.text('Waiting for upload');
            items[i].status = status;

            list.append(item);
        }
    };

    /**
     * Number of running uploads
     *
     * @type {number}
     */
    var process_running = 0;

    /**
     * Upload
     */
    var uploadFiles = function ()
    {
        var o_input = file_input[0];
        var number = o_input.files.length;
        if (!number) {
            file_input.click();
            return;
        }
        for (var i = 0; i < number; i++) {
            uploadFile(i);
        }
        button_group.hide();
        button_group_done.show();
    };

    /**
     * Upload one file
     *
     * @param i
     */
    var uploadFile = function (i)
    {
        var o_input = file_input[0];
        var item = items[i];
        item.status.hide();
        item.progress.show();
        var file = o_input.files[i];
        var form_data = new FormData();
        form_data.append('file', file);
        process_running++;
        $.ajax({
            url: url,
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
                            var percentage = (current * 100) / max;
                            item.progress_bar.css({
                                width: percentage + '%'
                            }).text(parseInt(percentage, 10) + '%');
                            if (percentage >= 100) {
                                item.progress_bar.removeClass('active');
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
                setTimeout(function ()
                {
                    if (data.status === 'ok') {
                        item.progress.hide();
                        item.status.show().addClass(class_prefix + 'success').text('Ok');
                        var clipb = $('<span class="capsule-cms-file-uploader-clipboard">');
                        clipb.attr('data-clipboard-text', data.url);
                        clipb.text(item.name.text());
                        clipb.prepend('<i class="fa fa-link"> ');
                        item.name.empty();
                        item.name.append(clipb);
                        var c = new Clipboard(clipb.get(0));
                    } else {
                        item.progress.hide();
                        item.status.show().addClass(class_prefix + 'fail').text(data.message);
                    }
                    process_running--;
                    if (process_running < 1) {
                        button_group_done.show();
                    }
                }, 500);
            },
            error: function (xhr, status, error)
            {
                setTimeout(function ()
                {
                    console.log(error);
                    item.progress.hide();
                    item.status.show().addClass(class_prefix + 'fail').text(error);
                    item.item.prop({
                        title: error
                    });
                    process_running--;
                    if (process_running < 1) {
                        button_group_done.show();
                    }
                }, 500);
            }
        });
    };
}

/**
 * Static method
 */
CapsuleCmsFileUploader.getInstance = function ()
{
    if (CapsuleCmsFileUploader.hasOwnProperty('instance')) {
        return CapsuleCmsFileUploader.instance;
    }
    return new CapsuleCmsFileUploader();
};
