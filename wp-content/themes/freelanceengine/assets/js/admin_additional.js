function attach_remove_modal(post_id, attach_id){
    let $ = jQuery;
    let $modal = $('#modal_delete_file');

    if ($modal.is(':visible')){
        $modal.hide();
    } else {
        $modal.find('.btn_submit_document').attr('onclick', 'attach_remove('+post_id+','+attach_id+')');
        $modal.show();
    }
}

function attach_remove(post_id, attach_id){
    let $ = jQuery;
    let $attach_item = $('#usp_attach_item_'+attach_id);
    let $modal = $('#modal_delete_file');
    let $submit = $modal.find('.btn_submit_document');
    let $success_message = $('.modal-footer');

    // disabling submit button
    $submit.prop('disabled', true);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
            action: 'usp_remove_attach',
            post_id: post_id,
            attach_id: attach_id
        },
        success: function (data) {
            $submit.removeAttr('disabled');
            $attach_item.remove();
            $success_message.slideDown();

            setTimeout(function(){
                $modal.hide();
            }, 2000);
        }
    });
}