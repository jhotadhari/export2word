<?php

// some ideas: https://solislab.com/blog/plugin-activation-checklist/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class E2wexport2word {
	
	const VERSION = 'taskRunner_setVersion';
	const DB_VERSION = 0;			// int	increase the number if the database needs an update
	const PLUGIN_SLUG = 'export2word';
	const PLUGIN_NAME = 'Export2Word';
	protected $deactivate_notice = '';
	protected $deps = array(
		'plugins' => array(
			/*
			'woocommerce' => array(
				'name'				=> 'WooCommerce',	// full name
				'ver_at_least'		=> '3.0.0',			// min version of required plugin
				'ver_tested_up_to'	=> '3.2.1',			// tested with required plugin up to
				'class'				=> 'WooCommerce',	// test by class
				//'function'		=> 'WooCommerce',	// test by function
			),
			*/
		),
		'php_version' => '5.6',						// required php version
		'wp_version' => '4.8',						// required wp version
	);
	protected $dependencies_ok = false;

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'on_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'on_uninstall' ) );
		
		add_action( 'plugins_loaded', array( $this, 'start_plugin' ), 9 );
		
	}
    
	public function start_plugin() {
		if ( $this->check_dependencies() ){
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			$this->register_post_types_and_taxs();
			$this->maybe_update();	// I think mass a plugin update does not run activation hooks
			add_action( 'plugins_loaded', array( $this, 'include_inc_dep_autoload' ) );
			add_action( 'plugins_loaded', array( $this, 'include_inc_fun_autoload' ) );
			do_action('e2w_plugin_loaded');
		} else {
			add_action( 'admin_init', array( $this, 'deactivate' ) );
		}
		
	}
	
	public function on_activate() {
		if ( $this->check_dependencies() ){
			$this->init_options();
			$this->register_post_types_and_taxs();
			$this->add_roles_and_capabilities();
			// hook the register post type functions, because init is to late
			do_action('e2w_on_activate_before_flush'); 			
			flush_rewrite_rules();
			$this->maybe_update();
			do_action('e2w_plugin_activated');
		} else {
			add_action( 'admin_init', array( $this, 'deactivate' ) );
			wp_die(
				$this->deactivate_notice
				. '<p>The plugin will not be activated.</p>'
				. '<p><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a></p>'
			);
		}
	}
	
	public function on_deactivate() {
		flush_rewrite_rules();
		do_action('e2w_plugin_deactivated');
	}
	
	public function on_uninstall() {
		do_action('e2w_plugin_uninstalled');	
	}
	
	protected function check_dependencies(){
		$error_msgs = array();
		
		// check php version
		if ( version_compare( PHP_VERSION, $this->deps['php_version'], '<') ){
			array_push( $error_msgs, 'PHP version ' . $this->deps['php_version'] . ' or higher.');
		}
		
		// check wp version
		// include an unmodified $wp_version
		include( ABSPATH . WPINC . '/version.php' );
		if ( version_compare( $wp_version, $this->deps['wp_version'], '<') ){
			array_push( $error_msgs, 'WordPress version ' . $this->deps['wp_version'] . ' or higher.');
		}		
		
		// check plugin dependencies
		if ( array_key_exists( 'plugins', $this->deps ) && is_array( $this->deps['plugins'] ) ){
			foreach ( $this->deps['plugins'] as $dep_plugin ){
				// check by class
				if ( array_key_exists( 'class', $dep_plugin ) && strlen( $dep_plugin['class'] ) > 0 ){
					if ( ! class_exists( $dep_plugin['class'] ) ) {
						array_push( $error_msgs, $dep_plugin['name'] . ' Plugin version ' . $dep_plugin['ver_at_least'] . ' (tested up to ' . $dep_plugin['ver_tested_up_to'] . ')');
					}
				}
				// check by function
				if ( array_key_exists( 'function', $dep_plugin ) && strlen( $dep_plugin['function'] ) > 0 ){
					if ( ! function_exists( $dep_plugin['function'] ) ) {
						array_push( $error_msgs, $dep_plugin['name'] . ' Plugin version ' . $dep_plugin['ver_at_least'] . ' (tested up to ' . $dep_plugin['ver_tested_up_to'] . ')');
					}
				}
			}
		}
		
		// maybe set deactivate_notice and deactivate plugin
		if ( count( $error_msgs ) > 0 ){
			$this->deactivate_notice = 
				'<h3>' . self::PLUGIN_NAME . ' plugin requires:</h3>'
				. '<ul style="padding-left: 1em; list-style: inside disc;"><li>' . implode ( '</li><li>' , $error_msgs ) . '</li></ul>';
			return false;
		}
	
		return true;
	}
	
	public function deactivate() {
		add_action( 'admin_notices', array( $this, 'the_deactivate_notice' ) );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	
	public function the_deactivate_notice(){
		echo '<div class="notice error">' . $this->deactivate_notice . '<p>The plugin will be deactivated.</p>' . '</div>';
	}
	
	protected function init_options() {
		update_option( 'e2w_version', self::VERSION );
		add_option( 'e2w_db_version', self::DB_VERSION );
	}
	
	// include files to register post types and taxonomies
	protected function register_post_types_and_taxs() {
		$this->include_dir( WP_PLUGIN_DIR . '/export2word/' . 'inc/post_types_taxs/autoload/' );
	}
	
	// include files to add user roles and capabilities
	protected function add_roles_and_capabilities() {
		$this->include_dir( WP_PLUGIN_DIR . '/export2word/' . 'inc/roles_capabilities/autoload/' );
	}	
	
	// check DB_VERSION and require the update class if necessary
	protected function maybe_update() {
		if ( get_option( 'e2w_db_version' ) < self::DB_VERSION ) {
			require_once( WP_PLUGIN_DIR . '/export2word/' . 'inc/dep/class-e2w-update.php' );
			new Testing_update();
		}
	}
	
	public function load_textdomain(){
		load_plugin_textdomain(
			'export2word',
			false,
			dirname( WP_PLUGIN_DIR . '/export2word/' . 'languages' )
		);
	}
	
	public function include_inc_dep_autoload() {
		$this->include_dir(  WP_PLUGIN_DIR . '/export2word/' . 'inc/dep/autoload/' );
	}
	
	public function include_inc_fun_autoload() {
		$this->include_dir(  WP_PLUGIN_DIR . '/export2word/' . 'inc/fun/autoload/' );
	}	
	
	public function include_dir( $directory ){
		$files =  glob( $directory . '*.php');
		if ( count($files) > 0 ){
			foreach ( $files as $file) {
				include_once( $file );
			}
		}
	}
	
 
}

global $e2w_export2word;
$e2w_export2word = new E2wexport2word();



?>