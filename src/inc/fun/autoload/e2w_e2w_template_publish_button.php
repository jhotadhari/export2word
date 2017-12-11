<?php

function e2w_e2w_template_editscreen_publish_button( $translation, $text ) {
    if ( 'e2w_template' == get_post_type() && ($text == 'Publish' || $text == 'Update') ) {
        return __('Save','export2word');
    } else {
        return $translation;
    }
}
add_filter( 'gettext', 'e2w_e2w_template_editscreen_publish_button', 10, 2 );
?>