<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// cmb2 init
function e2w_cmb2_init() {
	include_once( WP_PLUGIN_DIR . '/export2word/' . 'vendor/webdevstudios/cmb2/init.php' );
}
add_action('admin_init', 'e2w_cmb2_init', 3);
add_action('init', 'e2w_cmb2_init', 3);

?>