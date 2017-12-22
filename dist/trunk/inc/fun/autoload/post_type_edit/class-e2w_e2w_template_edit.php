<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		add_action( 'cmb2_admin_init', array( $this, 'editscreen_add_template_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editscreen_enqueue_style_script' ), 10, 1 );
	}
	
	public function editscreen_remove_submit_metabox() {
		foreach ( $this->object_types as $object_type ){
			remove_meta_box('submitdiv', $object_type, 'core');
		}
	}	
	
	public function editscreen_add_submit_metabox() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = 'e2w_tmpl_';
		
		// Initiate the metabox
		$cmb = new_cmb2_box( array(
			'id'			=> $prefix . 'submit',
			'title'			=> __( 'Save Template', 'export2word' ),
			'object_types'	=> $this->object_types,
			'context'		=> 'side',
			'priority'		=> 'default',
			'show_names'	=> true,
		) );
		
		$cmb->add_field( array(
			// 'name' => __('submit', 'export2word'),
			'id'   => $prefix . 'submit',
			'type'    => 'submit',
			'attributes' => array(
				'button_type' => 'primary button-large',
				'button_wrap' => false,
				'button_text' => 'Save Template',
			)
		) );		
	}
	
	public function editscreen_add_template_metabox() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = 'e2w_tmpl_';
		
		// Initiate the metabox
		$tmpl_settings = new_cmb2_box( array(
			'id'			=> $prefix . 'settings',
			'title'			=> __( 'Template Settings', 'export2word' ),
			'object_types'	=> $this->object_types,
			'context'		=> 'normal',
			'priority'		=> 'high',
			'show_names'	=> true,
		) );
		
		$tmpl_settings->add_field( array(
			'name'    => __('Content','export2word'),
			// 'desc'    => 'field description (optional)',
			'id'      => $prefix . 'content',
			'type'    => 'wysiwyg',
			'options' => array(
				'wpautop' => true, // use wpautop?
				'media_buttons' => true, // show insert/upload button(s)
				// 'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
				'textarea_rows' => 5,
				'tabindex' => '',
				'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
				'editor_class' => '', // add extra class(es) to the editor textarea
				'teeny' => false, // output the minimal editor config used in Press This
				'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
				'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
				'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
			),
		) );
		
		$tmpl_settings->add_field( array(
			'name' => __('Template shortcodes','export2word'),
			'id'   => 'mail_content_info_lead_import',
			'type' => 'info',	// requires remp
			'info' => 
				'{{Inhalte}} in doppelt geschweiften Klammern werden ersetzt.' . '<br>' . 
				'<br>' .
				'Sie können durch <i>objectdata</i> und <i>objectmeta</i> ersetzt werden.' . '<br>' .
				
				'Bspw. wird folgendes für den Title verwendet: <i>{{#objectdata#title}}</i>.' . '<br>' .
				'<br>' .
				'Es können auch Conditions verwendet werden. Dafür muss der Text mit <i>if</i> beginnen. Gültige Operanten sind <i>==</i> und <i>!=</i>.' . '<br>' .
				'Die Condition ist von der auszuführenden Action mit einem <i>::</i> getrennt. Die Action kann wieder wie oben aufgebaut sein.'
				. '<br>' .
				'Bsp: <i>{{if#meta#somemetakey==sometruevalue::#data#post_content}}</i>.' . '<br>' .
				'ist die Condition nicht erfüllt, wird nichts angezeigt.',
		) );
		
	}	
	
	public function editscreen_enqueue_style_script( $hook ) {
		global $post;
	
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( in_array( $post->post_type, $this->object_types ) ) {     
				wp_enqueue_style( 'e2w_template_edit', E2w_export2word::plugin_dir_url() .'/css/e2w_template_edit.min.css', false );
				wp_enqueue_script( 'e2w_template_edit', E2w_export2word::plugin_dir_url() .'/js/e2w_template_edit.min.js', array('jquery'));
				wp_enqueue_script( 'cmb2-conditionals', E2w_export2word::plugin_dir_url() .'/js/cmb2-conditionals.min.js', array( 'jquery', 'cmb2-scripts' ));
			}
		}
		
	}
	
}

function e2w_e2w_template_edit() {
	return E2w_e2w_template_edit::get_instance();
}

e2w_e2w_template_edit();

?>