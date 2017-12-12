<?php

Class E2w_e2w_template_edit {
	
	protected $object_types = array('e2w_template');
	
	protected static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}	
	
	protected function __construct() {
		// slience ...
	}
	
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'editscreen_remove_submit_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'editscreen_add_submit_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editscreen_enqueue_style_script' ), 10, 1 );
	}
	
	public function editscreen_remove_submit_metabox() {
		foreach ( $this->object_types as $object_type ){
			remove_meta_box('submitdiv', $object_type, 'core');
		}
	}	
	
	public function editscreen_add_submit_metabox() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = 'e2w_';
		
		// Initiate the metabox
		$cmb = new_cmb2_box( array(
			'id'			=> $prefix . 'submit',
			'title'			=> __( 'Save and Export', 'export2word' ),
			'object_types'	=> 'e2w_template',
			'context'		=> 'side',
			'priority'		=> 'default',
			'show_names'	=> true,
		) );
		
		$cmb->add_field( array(
			// 'name'    => __('save/export', 'export2word'),
			'id'      => $prefix . 'save_export',
			'type'    => 'radio_inline',
			'options' => array(
				'save' => __( 'Save only', 'export2word' ),
				'save_export'   => __( 'Save and Export to docx', 'export2word' ),
			),
			'default' => 'save',
		) );	
		
		$cmb->add_field( array(
			// 'name' => __('submit', 'export2word'),
			'id'   => $prefix . 'submit',
			'type'    => 'submit',
			'attributes' => array(
				'button_type' => 'primary button-large',
				'button_wrap' => false,
				'button_text' => 'Save',
			)
		) );		
	}
	
	public function editscreen_enqueue_style_script( $hook ) {
		global $post;
	
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( in_array( $post->post_type, $this->object_types ) ) {     
				wp_enqueue_style( 'e2w_template_edit', WP_PLUGIN_URL . '/export2word/css/e2w_template_edit.min.css', false );
				wp_enqueue_script( 'e2w_template_edit', WP_PLUGIN_URL . '/export2word/js/e2w_template_edit.min.js', array('jquery'));
			}
		}
		
	}
	
}


function e2w_e2w_template_edit() {
	return E2w_e2w_template_edit::get_instance();
}

e2w_e2w_template_edit();

?>