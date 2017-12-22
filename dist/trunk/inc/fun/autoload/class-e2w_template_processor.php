<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class E2w_Template_Processor {
	
	protected $queried_obj_type;
	protected $obj;
	protected $template;
	
	public function __construct( $queried_obj_type = 'none', $obj = null, $template ) {

		$this->queried_obj_type = $queried_obj_type;
		$this->obj = $obj;
		$this->template = $template;
		
	}
	
	public function process_template(){
	
		$return = preg_replace_callback(
			'/{{.*?}}/',
			function ($finds) {
				
				$find = substr( strip_tags($finds[0]), 2, -2);
				
				// escape "triple-stash" {{{ }}
				if ( strpos( $find, '{' ) == 0 && is_numeric( strpos( $find, '{' ) ) ){
					return '{{' . $find . '}}';
				}
				
				return $this->handle_markup( trim( $find ) );
			},
			$this->template
		);
		
		return $return;
		
	}
	
	protected function handle_markup( $str ){
		
		// if starts with condition
		if ( strpos( $str, 'if' ) == 0 && is_numeric( strpos( $str, 'if' ) ) ){
		
			preg_match_all('/(?<=#).*?(?=#|==|!=)/', $str, $key_val);
			preg_match_all('/(?<===|!=).*?(?=::)/', $str, $comparison);
			preg_match_all('/(?<=::).*/', $str, $action);
			
			if (
				isset($key_val[0]) && count($key_val[0]) >= 2
				&& isset($comparison[0][0])
				&& isset($action[0][0])
			){
				$val = $this->get_replacement( $key_val[0][0], $key_val[0][1] );
				
				preg_match_all('/(?<=' . $key_val[0][1] . ')../', $str, $condition);
				
				$condition = isset($condition[0][0]) ? $condition[0][0] : false;
				
				if (
					( $condition == '==' && $val == $comparison[0][0] )
					|| ( $condition == '!=' && $val != $comparison[0][0] )
				){
					$replacement = $this->handle_markup( $action[0][0] );
				} else {
					$replacement = '';
				}
			
			}
			
		} else {
		
			// replace with data|meta
			$parts = array_values( array_filter( explode( '#', $str ) , 'strlen') );
			if ( isset($parts) && count($parts) == 2 ){
				$replacement = $this->get_replacement( $parts[0], $parts[1] );
			}
			
		}
		
		return isset( $replacement ) ? $replacement : $str;
	}
	
	protected function get_replacement( $data_type, $key_id ) {
		
		switch ( $data_type ){
			case 'data':
				$replacement = $this->obj->$key_id;		
				break;
			case 'meta':
				switch ( $this->queried_obj_type ){
					case 'post':
						$replacement = get_post_meta( $this->obj->ID, $key_id, true);
						break;
					case 'term':
						$replacement = get_term_meta( $this->obj->ID, $key_id, true);
						break;
					case 'user':
						$replacement = get_user_meta( $this->obj->user_id, $key_id, true);
				}
				break;
			default:
				$replacement = null;
		}
		
		return $replacement;
	
	}
	
}

?>