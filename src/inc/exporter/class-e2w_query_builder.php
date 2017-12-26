<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// exchange 'this__' in args array recursive with $queried_obj data
Class E2w_Query_Builder {
	
	protected $queried_obj;
	protected $query_args;
	
	function __construct( $queried_obj = null, $query_args = null ){
	
		$this->queried_obj = $queried_obj;
		$this->query_args = $query_args;
		$this->exchange_r();
	
	}
	
	protected function exchange_r(){
		
		if ( $this->queried_obj === null || $this->query_args === null )
			return $this->query_args;
	
		array_walk_recursive ( $this->query_args , function( &$val, $key ){
			if ( strpos( $val, 'this__' ) === 0 ){
				$s = str_replace( 'this__', '', $val );
				$this->query_args[$key] = $this->queried_obj->$s;
			}
		});
	
	}
	
	public function get_query_args(){
		return $this->query_args;
	}
	
}

?>