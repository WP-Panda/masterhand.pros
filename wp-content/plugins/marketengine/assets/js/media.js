var plupload_config = {
    dragdrop: true,
    drop_element: "drop-element",
    browse_button: "uploader_browse",
    container: "gallery-container"
}
$.extend(true, plupload_config, plupload_opt);
// Initialize the widget when the DOM is ready
var uploader = new plupload.Uploader(plupload_config);
uploader.bind('FilesAdded', function(up, files) {
    var maxfiles = plupload_opt.max_files;
    if (up.files.length === parseInt(maxfiles)) {
        $(up.settings.browse_button).hide(); // provided there is only one #uploader_browse on page
    }
    if (up.files.length > maxfiles) {
        up.splice(maxfiles);
        alert(plupload_opt.error.max_files);
        up.removeFile(uploader.files[0]);
        up.refresh(); // must refresh for flash runtime
        return;
    }
    up.start();
});
uploader.init();