<?php

function e2w_e2w_template_editscreen_enqueue_style( $hook ) {
    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'e2w_template' === $post->post_type ) {     
            wp_enqueue_style( 'e2w_template_edit', WP_PLUGIN_URL . '/export2word/css/e2w_template_edit.min.css', false );
        }
    }    
    
}
add_filter( 'admin_enqueue_scripts', 'e2w_e2w_template_editscreen_enqueue_style', 10, 1 );
?>