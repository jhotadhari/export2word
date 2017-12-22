<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function cmb2_init_field_tree_properties() {

	class CMB2_Type_Tree_Properties extends CMB2_Type_Base {
	
		protected $localize_data = array();
		protected $node_properties_fields;
		
		public static function init() {
			add_filter( 'cmb2_render_class_tree_properties', array( __CLASS__, 'class_name' ) );
			// add_filter( 'cmb2_sanitize_tree_properties', array( __CLASS__, 'maybe_save_split_values' ), 12, 4 );
			
			/**
			 * The following snippets are required for allowing the tree_properties field
			 * to work as a repeatable field, or in a repeatable group
			 */
			add_filter( 'cmb2_sanitize_tree_properties', array( __CLASS__, 'sanitize' ), 10, 5 );
			add_filter( 'cmb2_types_esc_tree_properties', array( __CLASS__, 'escape' ), 10, 4 );
		}
		
		public static function class_name() { return __CLASS__; }
		
		public function render( $args = array() ) {
			
			// parse args
			$field = $this->field;
			$a = $this->parse_args( 'tree_properties', array(
				'id'      => $this->_id(),
				// 'value'   => $field->escaped_value( 'stripslashes' ),
				'desc'    => $this->_desc( true ),
				// 'options' => $field->options(),
				'attributes' => $field->attributes(),
			) );			

			// add node properties fields
			$this->node_properties_fields = $a['attributes']['fields'];
			
			// register scripts
			$this->register_scripts();
			
			ob_start();
			echo '<div class="cmb2-tree-properties-wrapper">';
			echo $this->render_tree();
			echo $this->render_node_properties();
			echo $this->render_tree_properties_data();
			echo '</div>';
			echo '<br class="clear">';
			
			$output = ob_get_clean();
			
			// enqueue_style_script
			add_action( 'admin_footer', array( $this, 'localize_scripts' ) );			
			add_action( 'admin_footer', array( $this, 'enqueue_style_script' ) );			
	
			// grab the data from the output buffer.
			return $this->rendered( $output );
		}
		
		protected function render_node_properties(){
			ob_start();
			
			echo '<div class="node-properties node-properties-wrapper">';
			
				// title
				echo '<div class="form-fields-box selected-node">';
					echo '<div class="selected-node-label form-fields-box-th"><h3>' . __('Selected Section', 'export2word') . '</h3></div>';
					echo '<div class="selected-node-name form-fields-box-td"><h3></h3></div>';
				echo '</div>'; 
				
				// hidden: selected node id
				echo $this->types->input( array(
					'type' => 'hidden',
					'class' => 'selected-node-id',
					'desc' => '',
				) );		
				
				// form
				echo '<div class="jsform jsform-wrapper">';
					echo $this->get_the_fields( $this->node_properties_fields );
				echo '</div>';
				
			echo '</div>';
				
			return ob_get_clean();
		}
		
		protected function get_the_fields( $fields= null ){
			
			if ( $fields === null ) return; 
			
			ob_start();
			
			foreach ( $fields as $field ) {
				echo '<div class="form-fields-box">';
					echo '<div class="conditional" data-eval="' . $field['depend'] . '___' . $field['depend_value'] . '">';
						echo '<div class="conditional-type-' . $field['key'] . '">';	
							
								// label
								echo '<div class="form-fields-box-th">';
								
									// title
									if ( $field['field_type'] === 'title' ){
										echo '<h3>' . $field['label'] . '</h3>';
										
									// all non title fields
									} else {
										echo $field['label'];
									} 
									
									// radio
									if ( $field['field_type'] === 'radio' ){
										foreach ( $field['options'] as $fields_options_key => $fields_options_val ) {
											if ( isset( $fields_options_val['desc'] ) && !empty($fields_options_val['desc']) ) {
												echo '<span class="desc show-on-input-hover desc-for-' . $field['key'] . '-' . $fields_options_key . '">' . $fields_options_val['desc'] . '</span>';
											}
										}
									}
									
								echo '</div>';
								
								echo '<div class="form-fields-box-td">';
									
									// radio
									if ( $field['field_type'] === 'radio' ){
										foreach ( $field['options'] as $fields_options_key => $fields_options_val ) {
											echo '<input name="data.' . $field['key'] . '" class="e2w-data-type hover-show-desc input-for-' . $field['key'] . '-' . $fields_options_key . '" value="' . $fields_options_key . '" type="radio">' . $fields_options_val['name'] . '<br>';
										}
									}
									
									// text
									if ( $field['field_type'] === 'text' ){
										echo '<input name="data.' . $field['key'] . '" class="e2w-data-type"><br>';
									}
									
									// textarea
									if ( $field['field_type'] === 'textarea' ){
										echo '<textarea name="data.' . $field['key'] . '" class="e2w-data-type"></textarea><br>';
									}									
									
									// all fields
									if ( isset( $field['desc'] ) && !empty($field['desc']) ) {
										echo '<span class="desc">' . $field['desc'] . '</span>';
									}										
								
							echo '</div>';
							
						echo '</div>';
					echo '</div>';
					
				echo '</div>';
			}
			
			return ob_get_clean();				
		
		}
		
		
		protected function render_tree(){	
			ob_start();
			echo '<div class="jstree jstree-wrapper"></div>';
			return ob_get_clean();		
		}
		
		protected function render_tree_properties_data(){
			ob_start();
			
			$debug = false;
			
			echo '<div class="tree-properties-data-wrapper">';
			
				if ( $debug ){
					echo $this->types->textarea( array(
						'class' => 'tree-properties-data',
						'rows'  => 20,
						) );
				} else {
					echo $this->types->input( array(
						'type' => 'hidden',
						'class' => 'tree-properties-data',
						) );
				}
			
			echo '</div>';
			
			return ob_get_clean();		
		}		
		
		public function register_scripts() {
			wp_register_script( 'jsForm',  E2w_export2word::plugin_dir_url() .'/vendor/corinis/jsForm/js/jquery.jsForm.min.js', array( 'jquery' ));
			wp_register_script( 'jstree',  E2w_export2word::plugin_dir_url() .'/js/jstree.min.js', array( 'jquery' ));
			wp_register_script( 'cmb2_field_type_tree_properties',  E2w_export2word::plugin_dir_url() .'/js/cmb2_field_type_tree_properties.min.js', array( 'jquery', 'jstree' ));
		}
		
		public function localize_scripts(){
			wp_localize_script( 'cmb2_field_type_tree_properties', 'tree_properties_data', $this->localize_data );
		}
		
		public function enqueue_style_script(){
			wp_enqueue_style( 'jstree',  E2w_export2word::plugin_dir_url() .'/css/jstree.min.css', false );
			wp_enqueue_style( 'jstree_custom',  E2w_export2word::plugin_dir_url() .'/css/cmb2_field_type_tree_properties.min.css', false );
			wp_enqueue_script( 'jsForm' );
			wp_enqueue_script( 'jstree' );
			wp_enqueue_script( 'cmb2_field_type_tree_properties' );
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
	CMB2_Type_Tree_Properties::init();	

}
add_action( 'cmb2_init', 'cmb2_init_field_tree_properties' );
