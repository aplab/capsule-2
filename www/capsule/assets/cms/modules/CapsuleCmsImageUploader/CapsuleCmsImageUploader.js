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

    var prefix = '.capsule-cms-image-uploader-';

    var w;

    this.showWindow = function ()
    {
        w = CapsuleCmsDialog.createElement('capsule-cms-image-uploader', {
            maximxze: 1,
            title: 'Upload images'
        });
        var footer = w.getFooter();
        var btn_wrapper_1 = $('<div class="capsule-cms-dialog-footer-button-3">');
        footer.append(btn_wrapper_1);
        var btn_wrapper_2 = $('<div class="capsule-cms-dialog-footer-button-3">');
        footer.append(btn_wrapper_2);
        var btn_wrapper_3 = $('<div class="capsule-cms-dialog-footer-button-3">');
        footer.append(btn_wrapper_3);
        var btn_cancel = $('<button type="button" id="apply-filter-by-container-btn" class="btn btn-default">Cancel</button>');
        btn_wrapper_1.append(btn_cancel);
        var btn_upload = $('<button type="button" id="apply-filter-by-container-btn" class="btn btn-warning">Upload</button>');
        btn_wrapper_2.append(btn_upload);
        var btn_browse = $('<button type="button" id="apply-filter-by-container-btn" class="btn btn-success">Browse</button>');
        btn_wrapper_3.append(btn_browse);
        w.show();
    }
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
}
