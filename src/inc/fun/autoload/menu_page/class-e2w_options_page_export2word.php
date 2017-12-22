<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * CMB2 Plugin Options
 * @version  0.0.1
 * @see      https://github.com/CMB2/CMB2-Snippet-Library/blob/59166b81693f4ab8651868e70cb29702576bd055/options-and-settings-pages/theme-options-cmb.php
 */
class E2w_options_page_export2word {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'export2word';
	
	private $tabs = array(
		
		'documents' => array(
			'documents' =>  'Documents',
			//'metabox_form_args' => array(
			//	'save_button' => __('Save','export2word')
			//)
		),
		
		'templates' => array(
			'templates' =>  'Templates',
			//'metabox_form_args' => array(
			//	'save_button' => __('Save','export2word')
			//)
		),
		
		'settings' => array(
			'settings' =>  'Settings',
			//'metabox_form_args' => array(
			//	'save_button' => __('Save','export2word')
			//)
		),
		
	);	

	/**
 	 * Options page metabox ids
 	 * @var array
 	 */
	private $metabox_ids = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Pages hook
	 * @var array
	 */
	protected $options_pages = array();

	/**
	 * Holds an instance of the object
	 *
	 * @var E2w_options_page_export2word
	 */
	protected static $instance = null;
	
	
	// template WP_List_Table object
	public $templates_obj;	
	// documents WP_List_Table object
	public $documents_obj;	

	/**
	 * Returns the running object
	 *
	 * @return E2w_options_page_export2word
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 * @since 0.0.1
	 */
	protected function __construct() {
		// Set our title
		$this->title = __( 'Export2Word', 'export2word' );
		
		foreach( $this->tabs as $key => $val ) {
			$this->metabox_ids[$key] = array( 'metabox_id'	=>	$this->key . '_' . $key );
			foreach( $val as $k => $v ) {
				$this->metabox_ids[$key][$k] = $v;
			}
		}		
	}

	/**
	 * Initiate our hooks
	 * @since 0.0.1
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		
		foreach( $this->metabox_ids as $key => $val ) {
			add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' . '__' . $key ) );
			add_action( 'cmb2_after_options-page_form_' . $val['metabox_id'], array( $this, 'enqueue_style_script'), 10, 2 );
		}		
		
		add_action( 'cmb2_after_init', array( $this, 'handle_submission') );
	}
	
	/**
	 * Enqueue styles and scripts
	 * @since 0.0.1
	 */	
	public function enqueue_style_script( $post_id, $cmb ) {
		wp_enqueue_style( 'e2w_options_page_export2word', E2w_export2word::plugin_dir_url() .'/css/e2w_options_page_export2word.min.css', false );
		wp_enqueue_script('e2w_options_page_export2word', E2w_export2word::plugin_dir_url() .'/js/e2w_options_page_export2word.min.js', array( 'jquery' ));
	}	

	/**
	 * Register our setting to WP
	 * @since  0.0.1
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.0.1
	 */
	public function add_options_page() {
		
		$this->options_page = add_submenu_page( 
			'tools.php', 
			$this->title, 
			$this->title, 
			'manage_options', 
			$this->key, 
			array( $this, 'admin_page_display' )
		);
		
		add_action( "load-$this->options_page", array( $this, 'screen_option' ) );
		
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}
	
	public function screen_option() {
	
		$option = 'per_page';
		$args   = array(
			'label'   => 'Templates',
			'default' => 5,
			'option'  => 'templates_per_page'
		);
	
		add_screen_option( $option, $args );
	
		$this->templates_obj = new E2w_List_Table_Templates();
		$this->documents_obj = new E2w_List_Table_Documents();
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.0.1
	 */
	public function admin_page_display() {
		
		// get active tab
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : array_keys( $this->metabox_ids )[0];
		
		// wrappers
		$wrapper_start = '';
		$wrapper_start .= '<div class="wrap cmb2-options-page ' . $this->key . '">';
			$wrapper_start .= '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
			// navigation tabs
			$wrapper_start .= '<h2 class="nav-tab-wrapper">';
				foreach( $this->metabox_ids as $key => $val) {
					$wrapper_start .= '<a href="?page=export2word&tab=' . $key . '" class="nav-tab' . ($key === $active_tab ? ' nav-tab-active' : '') . '">' . __( $val[$key], 'export2word') . '</a>';
				}
			$wrapper_start .= '</h2>';
		$wrapper_end = '</div>';
		
		echo $wrapper_start;
		
		if ( $active_tab == 'templates' ) {
			
			// new template button
			echo '<br>';
			$new_template_link = admin_url('post-new.php?post_type=e2w_template');
			echo '<a href="' . $new_template_link . '" class="page-title-action">' . __( 'Create a new Template', 'export2word' ) . '</a>';
			echo '<br>';
			
			$this->templates_obj->prepare_items();
			
			$this->templates_obj->views();
			
			// search box
			echo '<form method="post">';
				echo '<input type="hidden" name="page" value="' . $this->key  . '" />';
				$this->templates_obj->search_box('Search', 'ID');
			echo '</form>';
			
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								
								$this->templates_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<?php	
		
		} elseif ( $active_tab == 'documents' ) {
			
			// new document button
			echo '<br>';
			$new_document_link = admin_url('post-new.php?post_type=e2w_document');
			echo '<a href="' . $new_document_link . '" class="page-title-action">' . __( 'Create a new Document', 'export2word' ) . '</a>';
			echo '<br>';
			
			$this->documents_obj->prepare_items();
			
			$this->documents_obj->views();
			
			// search box
			echo '<form method="post">';
				echo '<input type="hidden" name="page" value="' . $this->key  . '" />';
				$this->documents_obj->search_box('Search', 'ID');
			echo '</form>';
			
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								
								$this->documents_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<?php			
			
		} else {
		// form
		cmb2_metabox_form(
			$this->metabox_ids[$active_tab]['metabox_id'],
			$this->key,
			isset( $this->metabox_ids[$active_tab]['metabox_form_args'] ) ? $this->metabox_ids[$active_tab]['metabox_form_args'] : array()
		);
		}
	
		echo $wrapper_end;

	}
	
	public function add_options_page_metabox__settings() {
		$tab = 'settings';
		
		$metabox_id = $this->key . '_' . $tab;
		
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );
		
		// Set our CMB2 fields
		$cmb->add_field( array(
			'name' => __( 'Test Text', 'export2word' ),
			'desc' => __( 'field description (optional)', 'export2word' ),
			'id'   => 'settings_test_text',
			'type' => 'text',
			'default' => 'Default Text',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Test Color Picker', 'export2word' ),
			'desc'    => __( 'field description (optional)', 'export2word' ),
			'id'      => 'settings_test_colorpicker',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		) );
	
	}
	
	public function add_options_page_metabox__documents() {
		$tab = 'documents';
		
		$metabox_id = $this->key . '_' . $tab;
		
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );
	
	}	
	
	public function add_options_page_metabox__templates() {
		$tab = 'templates';
		
		$metabox_id = $this->key . '_' . $tab;
		
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'         => $metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );
	
	}
	
	protected function get_metabox_by_nonce( $nonce, $return = 'metabox' ) {
		if (! $nonce || ! strpos($nonce, 'nonce_CMB2php') === 0 )
			return false;
		
		$metabox_id = str_replace( 'nonce_CMB2php', '', $nonce );
		
		switch ( $return ){
			case 'metabox':
				return cmb2_get_metabox( $metabox_id, $this->key );
				break;
			case 'metabox_id':
				return $metabox_id;
				break;				
			case 'tab_name':
				return str_replace( $this->key . '_', '', $metabox_id );
				break;
			default:
				// silence ...
		}
		
	}
	
	public function handle_submission() {
		
		// is form submission?
		if ( empty( $_POST ) || ! isset( $_POST['submit-cmb'], $_POST['object_id'] ) ) return false;
		// is export2word form submission?
		if ( ! $_POST['object_id'] == $this->key ) return false;
		
		// get nonce, metabox, tab_name
		$nonce = array_keys( $this->preg_grep_keys('/nonce_CMB2php\w+/', $_POST ) )[0];
		$tab_name = $this->get_metabox_by_nonce( $nonce, 'tab_name');
		$cmb = $this->get_metabox_by_nonce( $nonce );
		if (! $cmb ) return false;
		
		// Check security nonce
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			new Remp_Admin_Notice( array('Something went wrong.','Nonce verification failed.'), true );
			return;
		}
		
		// Fetch sanitized values
		$sanitized_values = $cmb->get_sanitized_values( $_POST );

		switch ( $tab_name ){
			
			case 'documents':
				break;
				
			case 'templates':
				break;

			case 'settings':
				break;
				
			default:
				// silence ...
		}
		
	}
	
	public function preg_grep_keys( $pattern, $input, $flags = 0 ){
		$keys = preg_grep( $pattern, array_keys( $input ), $flags );
		$vals = array();
		foreach ( $keys as $key )    {
			$vals[$key] = $input[$key];
		}
		return $vals;
	}	

	/**
	 * Register settings notices for display
	 *
	 * @since  0.0.1
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'export2word' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.0.1
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the E2w_options_page_export2word object
 * @since  0.0.1
 * @return E2w_options_page_export2word object
 */
function e2w_options_page_export2word() {
	return E2w_options_page_export2word::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.0.1
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function e2w_export2word_get_option( $key = '', $default = null ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( e2w_options_page_export2word()->key, $key, $default );
	}

	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( e2w_options_page_export2word()->key, $key, $default );

	$val = $default;

	if ( gettype($opts) === 'array' && !empty($opts) ){
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
	}

	return $val;
}

// Get it started
e2w_options_page_export2word();

?>