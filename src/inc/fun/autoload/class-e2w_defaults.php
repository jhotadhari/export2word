<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class E2w_defaults {

	protected $defaults = array();

	public function add_default( $arr ){
		$defaults = $this->defaults;
		$this->defaults = array_merge( $defaults , $arr);
	}
	
	public function get_default( $key ){
		if ( array_key_exists($key, $this->defaults) ){
			return $this->defaults[$key];

		}
			return null;
	}

}

function e2w_init_defaults(){
	global $e2w_defaults;
	
	$e2w_defaults = new E2w_defaults();
	
	// $defaults = array(
	// 	// silence ...
	// );
	
	// $e2w_defaults->add_default( $defaults );	
}
add_action( 'admin_init', 'e2w_init_defaults', 1 );
add_action( 'init', 'e2w_init_defaults', 1 );


?>