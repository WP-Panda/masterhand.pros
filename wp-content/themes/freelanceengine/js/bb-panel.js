$('#simple-bbcode-editor').bbcodeditor({
    defaultValue: "", // This option must be included whenever the editor is called (it can be left empty) !important
    includedButtons: [['bold', 'italic', 'underline'], ['strikethrough', 'supperscript', 'subscript']],
});

$('.bbcodeditor-body-content').keyup(function () {
    let bb_editor_content = $('.bbcodeditor-body-content').val();
    $('#user-submitted-content').val(bb_editor_content);
});

$('.btn-toolbar .btn').click(function () {
    let bb_editor_content = $('.bbcodeditor-body-content').val();
    $('#user-submitted-content').val(bb_editor_content);
});