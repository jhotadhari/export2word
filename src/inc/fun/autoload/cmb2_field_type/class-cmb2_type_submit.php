<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function cmb2_init_field_subimt() {

	class CMB2_Type_Submit extends CMB2_Type_Base {
	
		public static function init() {
			add_filter( 'cmb2_render_class_submit', array( __CLASS__, 'class_name' ) );
	
			/**
			 * The following snippets are required for allowing the submit field
			 * to work as a repeatable field, or in a repeatable group
			 */
			add_filter( 'cmb2_sanitize_submit', array( __CLASS__, 'sanitize' ), 10, 5 );
			add_filter( 'cmb2_types_esc_submit', array( __CLASS__, 'escape' ), 10, 4 );
		}
		
		public static function class_name() { return __CLASS__; }
		
		public function render( $args = array() ) {
			
			// wrapper parameters
			$button_wrapper_id = array_key_exists('button_wrapper_id', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_wrapper_id'] : 'publishing-action';
			// hidden input parameters
			$button_hidden_name_id = array_key_exists('button_hidden_name_id', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_hidden_name_id'] : 'original_publish';
			$button_hidden_value = array_key_exists('button_hidden_value', $this->field->args( 'attributes' ) ) ? $button_hidden_value = $this->field->args( 'attributes' )['button_hidden_value'] : $button_hidden_value = $_GET['action'] == 'edit' ? 'Update' : esc_attr('Publish');
			// submit button parameters
			$button_text = array_key_exists('button_text', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_text'] : null;
			$button_type = array_key_exists('button_type', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_type'] : 'primary';
			$button_name = array_key_exists('button_name', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_name'] : 'submit';
			$button_wrap = array_key_exists('button_wrap', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_wrap'] : true;
			$button_other_attributes = array_key_exists('button_other_attributes', $this->field->args( 'attributes' ) ) ?  $this->field->args( 'attributes' )['button_other_attributes'] : null;
			
			ob_start();
			
				echo '<div id="' . $button_wrapper_id . '">';
					echo '<span class="spinner"></span>';
					echo '<input name="' . $button_hidden_name_id . '" id="' . $button_hidden_name_id . '" value="' . $button_hidden_value . '" type="hidden">';
					echo get_submit_button(
						$button_text,				// $text				null
						$button_type,				// $type				'primary'
						$button_name,				// $name				'submit'
						$button_wrap,				// $wrap				true
						$button_other_attributes	// $other_attributes	null	array
					);
				echo '</div>';
				
				// hidden field. nonsence, just possibility to set data-conditional
				echo $this->types->input( array(
					'type' => 'hidden',
				) );						
				
			$output = ob_get_clean();
			
			// enqueue_style_script
			add_action( 'admin_footer', array( $this, 'enqueue_style_script' ) );			
	
			// grab the data from the output buffer.
			return $this->rendered( $output );
		}
		
		public function enqueue_style_script(){
			// silence ...
		}
		
		public static function sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
	
			// if not repeatable, bail out.
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
	
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
			}
	
			return array_filter($meta_value);
		}		
		
		public static function escape( $check, $meta_value, $field_args, $field_object ) {
			// if not repeatable, bail out.
			if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
				return $check;
			}
	
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
			}
	
			return array_filter($meta_value);
		}		
		
	}
	
	// init class
	CMB2_Type_Submit::init();	

}
add_action( 'cmb2_init', 'cmb2_init_field_subimt' );
