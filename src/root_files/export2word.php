<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class E2w_Export2word {
	
	const VERSION = 'taskRunner_set_version';
	const DB_VERSION = 0;			// int	increase the number if the database needs an update
	const PLUGIN_SLUG = 'export2word';
	const PLUGIN_NAME = 'Export2Word';
	protected $deactivate_notice = '';
	protected $deps = array(
		'plugins' => array(
			/*
			'woocommerce' => array(
				'name'				=> 'WooCommerce',				// full name
				'link'				=> 'https://woocommerce.com/',	// link
				'ver_at_least'		=> '3.0.0',						// min version of required plugin
				'ver_tested_up_to'	=> '3.2.1',						// tested with required plugin up to
				'class'				=> 'WooCommerce',				// test by class
				//'function'		=> 'WooCommerce',				// test by function
			),
			*/
		),
		'php_version' => 'taskRunner_set_phpRequiresAtLeast',		// required php version
		'wp_version' => 'taskRunner_set_wpRequiresAtLeast',			// required wp version
		'php_ext' => array(
			'xml' => array(
				'name'				=> 'Xml',											// full name
				'link'				=> 'http://php.net/manual/en/xml.installation.php',	// link
			),
		),
	);
	protected $dependencies_ok = false;

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'on_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivate' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'on_uninstall' ) );
		add_action( 'plugins_loaded', array( $this, 'start_plugin' ), 9 );
	}
	
	public static function plugin_dir_url(){
		return plugins_url( '', __FILE__ );		// no trailing slash
	}
	
	public static function plugin_dir_path(){
		return plugin_dir_path( __FILE__ );		// trailing slash
	}
	
	public static function plugin_dir_basename(){
		return basename( dirname( __FILE__ ) );	// no trailing slash
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
	
	public static function on_uninstall() {
		do_action('e2w_plugin_uninstalled');	
	}
	
	protected function check_dependencies(){
		$error_msgs = array();
		
		// check php version
		if ( version_compare( PHP_VERSION, $this->deps['php_version'], '<') ){
			$err_msg = sprintf( 'PHP version %s or higher', $this->deps['php_version'] );
			array_push( $error_msgs, $err_msg );
		}
		
		// check php extensions
		if ( array_key_exists( 'php_ext', $this->deps ) && is_array( $this->deps['php_ext'] ) ){
			foreach ( $this->deps['php_ext'] as $php_ext_key => $php_ext_val ){
				if ( ! extension_loaded( $php_ext_key ) ) {
					$err_msg = sprintf( '<a href="%s" target="_blank">%s</a> php extension to be installed', $php_ext_val['link'], $php_ext_val['name'] );
					array_push( $error_msgs, $err_msg );
				}
			}
		}
		
		// check wp version
		// include an unmodified $wp_version
		include( ABSPATH . WPINC . '/version.php' );
		if ( version_compare( $wp_version, $this->deps['wp_version'], '<') ){
			$err_msg = sprintf( 'WordPress version %s or higher', $this->deps['wp_version'] );
			array_push( $error_msgs, $err_msg );
		}		
		
		// check plugin dependencies
		if ( array_key_exists( 'plugins', $this->deps ) && is_array( $this->deps['plugins'] ) ){
			foreach ( $this->deps['plugins'] as $dep_plugin ){
				$err_msg = sprintf( ' <a href="%s" target="_blank">%s</a> Plugin version %s (tested up to %s)', $dep_plugin['link'], $dep_plugin['name'], $dep_plugin['ver_at_least'], $dep_plugin['ver_tested_up_to']);
				// check by class
				if ( array_key_exists( 'class', $dep_plugin ) && strlen( $dep_plugin['class'] ) > 0 ){
					if ( ! class_exists( $dep_plugin['class'] ) ) {
						array_push( $error_msgs, $err_msg );
					}
				}
				// check by function
				if ( array_key_exists( 'function', $dep_plugin ) && strlen( $dep_plugin['function'] ) > 0 ){
					if ( ! function_exists( $dep_plugin['function'] ) ) {
						array_push( $error_msgs, $err_msg);
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
		self::include_dir( self::plugin_dir_path() . 'inc/post_types_taxs/autoload/' );
	}
	
	// include files to add user roles and capabilities
	protected function add_roles_and_capabilities() {
		self::include_dir( self::plugin_dir_path() . 'inc/roles_capabilities/autoload/' );
	}	
	
	// check DB_VERSION and require the update class if necessary
	protected function maybe_update() {
		if ( get_option( 'e2w_db_version' ) < self::DB_VERSION ) {
			// require_once( self::plugin_dir_path() . 'inc/dep/class-e2w-update.php' );
			// new E2w_Update();
			// class E2w_Update is missing ??? !!!
		}
	}
	
	public function load_textdomain(){
		load_plugin_textdomain(
			'export2word',
			false,
			self::plugin_dir_basename() . '/languages' 
		);
	}
	
	public function include_inc_dep_autoload() {
		self::include_dir(  self::plugin_dir_path() . 'inc/dep/autoload/' );
	}
	
	public function include_inc_fun_autoload() {
		self::include_dir(  self::plugin_dir_path() . 'inc/fun/autoload/' );
	}
	
	public static function rglob( $pattern, $flags = 0) {
		$files = glob( $pattern, $flags ); 
		foreach ( glob( dirname( $pattern ).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir ) {
			$files = array_merge( $files, self::rglob( $dir.'/'.basename( $pattern ), $flags ) );
		}
		return $files;
	}	
	
	public static function include_dir( $directory ){
		$files =  self::rglob( $directory . '*.php');
		if ( count($files) > 0 ){
			foreach ( $files as $file) {
				include_once( $file );
			}
		}
	}
 
}

global $e2w_export2word;
$e2w_export2word = new E2w_Export2word();



?>