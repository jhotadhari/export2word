<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class E2w_e2w_document_edit {
	
	protected $object_types = array('e2w_document');
	
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
		add_action( 'cmb2_admin_init', array( $this, 'editscreen_add_document_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'editscreen_enqueue_style_script' ), 10, 1 );
	}
	
	public function editscreen_remove_submit_metabox() {
		foreach ( $this->object_types as $object_type ){
			remove_meta_box('submitdiv', $object_type, 'core');
		}
	}	
	
	public function editscreen_add_submit_metabox() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = 'e2w_doc_';
		
		// Initiate the metabox
		$submit = new_cmb2_box( array(
			'id'			=> $prefix . 'submit',
			'title'			=> __( 'Save and Export', 'export2word' ),
			'object_types'	=> $this->object_types,
			'context'		=> 'side',
			'priority'		=> 'default',
			'show_names'	=> true,
		) );
		
		$submit->add_field( array(
			// 'name'    => __('save/export', 'export2word'),
			'id'      => $prefix . 'save_export',
			'type'    => 'radio_inline',
			'options' => array(
				'save' => __( 'Save only', 'export2word' ),
				'save_export'   => __( 'Save and Export to docx', 'export2word' ),
			),
			'default' => 'save',
		) );	
		
		$submit->add_field( array(
			// 'name' => __('submit', 'export2word'),
			'id'   => $prefix . 'submit_btn',
			'type'    => 'submit',
			'attributes' => array(
				'button_type' => 'primary button-large',
				'button_wrap' => false,
				'button_text' => 'Save',
			)
		) );		
	}
	
	public function editscreen_add_document_metabox() {
		// Start with an underscore to hide fields from custom fields list
		$prefix = 'e2w_doc_';
		
		// Initiate the metabox
		$doc_structure = new_cmb2_box( array(
			'id'			=> $prefix . 'doc_structure',
			'title'			=> __( 'Document structure', 'export2word' ),
			'object_types'	=> $this->object_types,
			'context'		=> 'normal',
			'priority'		=> 'high',
			'show_names'	=> true,
		) );
		
		$doc_structure->add_field( array(
			'id'   => $prefix . 'tree_properties',
			'type' => 'tree_properties',
			'desc' => __( 'Right click on a Section to edit.', 'export2word' ),
			'attributes' => array(
				'fields' => $this->tree_properties_fields(),
			)
			
		) );
		
	}	
	
	protected function tree_properties_fields(){
	
		$fields = array();
		
		$fields[] = array(
			'label' => __( 'Type Settings', 'export2word' ),
			'key' => 'type_settings',
			'field_type' => 'title',
		);		
		
		$fields[] = array(
			'label' => __( 'Section Type', 'export2word' ),
			'key' => 'section_type',
			'field_type' => 'radio',
			'desc' => __( 'bla bla ???', 'export2word' ),
			'options' => array(
				
				'query' => array(
					'name' => __( 'Query', 'export2word' ),
					'desc' => 
						__( 'Query the database for post, term or users.', 'export2word' ) . '<br>' .
						__( 'The child sections represent the loop and recieve the queried object', 'export2word' ) . '<br>' .
						__( 'Will recieve the queried object, if used as a child of a "Query Section".', 'export2word' )
						,
				),
			
				'singular' => array(
					'name' => __( 'Singular', 'export2word' ),
					'desc' => 
						__( 'For a single post, term or user (or template withut queried object).', 'export2word' ) . '<br>' .
						__( 'Will recieve the queried object, if used as a child of a "Query Section".', 'export2word' )
						,
				),
	
				'table_of_content' => array(
					'name' => __( 'Table of content', 'export2word' ),
					'desc' => __( 'Insert a table of content.', 'export2word' ),
				),
				
			),
		);
			
		$fields[] = array(
			'label' => __( 'Query Type', 'export2word' ),
			'key' => 'query_type',
			'field_type' => 'radio',
			'depend' => 'section_type',
			'depend_value' => 'query',
			'options' => array(
				'query_post' => array(
					'name' => __( 'Post Query', 'export2word' ),
					'desc' => __( 'Query the database for posts.', 'export2word' ),
				),
				'query_term' => array(
					'name' => __( 'Term Query', 'export2word' ),
					'desc' => __( 'Query the database for terms.', 'export2word' ),
				),
				'query_user' => array(
					'name' => __( 'User Query', 'export2word' ),
					'desc' => __( 'Query the database for users.', 'export2word' ),
				),
			),
		);
		
		/*					
		$fields[] = array(
			'label' => __( 'Query Input', 'export2word' ),
			'key' => 'query_input',
			'field_type' => 'radio',
			'desc' => __( 'Use a graphical User Interface to build the Query or build a custom Query with JSON', 'export2word' ),
			'depend' => 'section_type',
			'depend_value' => 'query',
			'options' => array(
				'gui' => array(
					'name' => __( 'GUI', 'export2word' ),
					// 'desc' => __( 'Use a graphical User Interface to build the Query.', 'export2word' ),
				),
				'json' => array(
					'name' => __( 'JSON', 'export2word' ),
					// 'desc' => __( 'Use a JSON formatted array to build the Query arguments.', 'export2word' ),
				),
			),
		);
		*/
		
		$fields[] = array(
			'label' => __( 'Query Arguments', 'export2word' ),
			'key' => 'query_args',
			'field_type' => 'textarea',
			// 'depend' => 'query_input',
			// 'depend_value' => 'json',
			'depend' => 'section_type',
			'depend_value' => 'query',			
			'desc' => __( 'The Query arguments as json formatted array.', 'export2word' ),
		);
		
		$fields[] = array(
			'label' => __( 'Content Settings', 'export2word' ),
			'key' => 'content_settings',
			'field_type' => 'title',
		);
		
		$fields[] = array(
			'label' => __( 'Template ID', 'export2word' ),
			'key' => 'template_id',
			'field_type' => 'text',
			// 'desc' => __( 'The Query arguments as json formatted array.', 'export2word' ),
		);
		
		$fields[] = array(
			'label' => __( 'Style Settings', 'export2word' ),
			'key' => 'style_settings',
			'field_type' => 'title',
		);
			
		$fields[] = array(
			'label' => __( 'Inherit Style', 'export2word' ),
			'key' => 'inherit_style',
			'field_type' => 'radio',
			'options' => array(
				'yes' => array(
					'name' => __( 'Yes', 'export2word' ),
				),
				'no' => array(
					'name' => __( 'No', 'export2word' ),
				),
			),
		);		
		
		$fields[] = array(
			'label' => __( 'Page break', 'export2word' ),
			'key' => 'page_break',
			'field_type' => 'radio',
			'depend' => 'inherit_style',
			'depend_value' => 'no',				
			'options' => array(
				'nextPage' => array(
					'name' => __( 'New page', 'export2word' ),
				),
				'continuous' => array(
					'name' => __( 'Continue same page', 'export2word' ),
				),
			),
		);
		
		$fields[] = array(
			'label' => __( 'Page Orentation', 'export2word' ),
			'key' => 'orientation',
			'field_type' => 'radio',
			'depend' => 'inherit_style',
			'depend_value' => 'no',	
			'options' => array(
				'portrait' => array(
					'name' => __( 'Portrait', 'export2word' ),
				),
				'landscape' => array(
					'name' => __( 'Landscape', 'export2word' ),
				),
			),
		);		
	
		return $fields;
	
	}
	
	public function editscreen_enqueue_style_script( $hook ) {
		global $post;
	
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( in_array( $post->post_type, $this->object_types ) ) {     
				wp_enqueue_style( 'e2w_document_edit', WP_PLUGIN_URL . '/export2word/css/e2w_document_edit.min.css', false );
				wp_enqueue_script( 'e2w_document_edit', WP_PLUGIN_URL . '/export2word/js/e2w_document_edit.min.js', array('jquery'));
				wp_enqueue_script( 'cmb2-conditionals', WP_PLUGIN_URL . '/export2word/js/cmb2-conditionals.min.js', array( 'jquery', 'cmb2-scripts' ));
			}
		}
		
	}
	
}

function e2w_e2w_document_edit() {
	return E2w_e2w_document_edit::get_instance();
}

e2w_e2w_document_edit();

?>