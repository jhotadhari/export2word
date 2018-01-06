<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// _integration_cmb2_qtranslate_init
function e2w_integration_cmb2_qtranslate_init() {
	wp_enqueue_script('cmb2_qtranslate_main', E2w_Export2word::plugin_dir_url() . '/vendor/jmarceli/integration-cmb2-qtranslate/dist/scripts/main.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'e2w_integration_cmb2_qtranslate_init');

?>