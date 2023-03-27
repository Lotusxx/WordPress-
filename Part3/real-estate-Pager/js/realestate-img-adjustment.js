jQuery(document).ready(
    function($) {
        let _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

        // 画像の表示
        $( '.media_button' ).each(function(index) {
            $(this).on("click", function(){
                let send_attachment_bkp = wp.media.editor.send.attachment;
                wp.media.editor.send.attachment = function(props, attachment){
                    if ( _custom_media ) {
                        $('#realestate-image_'+index).val(attachment.id);
                        $('#image-wrapper_'+index).html('<img class="custom_media_image" src="' + attachment.sizes.thumbnail.url + '" height="' + attachment.sizes.thumbnail.height + '" width="' + attachment.sizes.thumbnail.width + '">');
                    } else {
                        return _orig_send_attachment.apply( $(this).id, [props, attachment] );
                    }
                }
                wp.media.editor.open($(this));
                return false;
            });
        });

        // 削除
        $( '.media_remove').each(function(index) {
            $(this).on("click", function(){
                $('#realestate-image_'+index).val('');
                $('#image-wrapper_' + index + ' .custom_media_image').remove();
            });
        });
    });