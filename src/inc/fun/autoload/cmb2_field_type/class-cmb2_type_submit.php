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
			
			$field = $this->field;
			// $field_attributes = $field->attributes();
			
			$action = isset( $_GET['action'] ) && gettype( $_GET['action'] ) === 'string' && $_GET['action'] === 'edit' ? 'edit' : 'new';
			
			// parse params
			$params = wp_parse_args( $field->attributes(), array(
				// wrapper parameters
				'wrapper_id' => 'publishing-action',
				// hidden input parameters
				'hidden_name_id' => 'original_publish',
				'hidden_value' => $action === 'edit' ? esc_attr( 'Update' ) : esc_attr( 'Publish' ),
				// submit button parameters
				'btn_type' => 'primary',
				'btn_text' => $action === 'edit' ? esc_attr__( 'Save', 'export2word' ) : esc_attr__( 'Update', 'export2word' ),
				'btn_wrap' => false,
				'btn_name' => $action === 'edit' ? esc_attr( 'save' ) : esc_attr( 'publish' ),
			) );
			// submit button other attributes
			$params['btn_other_attributes'] = wp_parse_args( $field->attributes()['btn_other_attributes'], array(
				'id' => esc_attr( 'publish' ),
				'accesskey' => esc_attr( 'p' ),
			) );
			
			ob_start();
			
				echo sprintf( '<div id="%s">', esc_attr( $params['wrapper_id'] ) );				
					echo '<span class="spinner"></span>';
					
					echo sprintf(
						'<input name="%s" id="%s" value="%s" type="hidden">',
						esc_attr( $params['hidden_name_id'] ),
						esc_attr( $params['hidden_name_id'] ),
						esc_attr( $params['hidden_value'] ),
						esc_attr( 'hidden' )
					);
					
					echo get_submit_button(
						esc_attr( $params['btn_text'] ),	// $text				null
						esc_attr( $params['btn_type'] ),	// $type				'primary'
						esc_attr( $params['btn_name'] ),	// $name				'submit'
						$params['btn_wrap'],				// $wrap				true
						$params['btn_other_attributes']	// $other_attributes	null	array
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
