<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// cmb2 init
function e2w_cmb2_init() {
	include_once( E2w_export2word::plugin_dir_path() . 'vendor/webdevstudios/cmb2/init.php' );
}
add_action('admin_init', 'e2w_cmb2_init', 3);
add_action('init', 'e2w_cmb2_init', 3);

?>