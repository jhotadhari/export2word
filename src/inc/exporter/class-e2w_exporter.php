<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class E2w_Exporter {
	
	private $document_id;
	private $PhpWord;
	private $writer;
	private $filename;
	private $temp_file_uri;
	private $args;
	// private $error_msgs = array();
	
	public function __construct( $document_id = null, $args = array() ) {
		
		// stop if no document_id is given
		if ( $document_id === null )
			return false;
		
		// set document_id
		$this->document_id = $document_id;
		
		// parse args
		$defaults = array(
			// 'blala' => 'is doch egal',
		);	
		$this->args = wp_parse_args( $args, $defaults );		
		
		// is verified? 
		if ( ! $this->verify() )
			return false;
		
		// init
		$this->initialize();
		
	}
	
	private function verify(){
		
		// check if post_type is e2w_document
		if ( get_post_type( $this->document_id ) != 'e2w_document' )
			return false;		
		
		// check if document should be exported
		if ( get_post_meta( $this->document_id, 'e2w_doc_save_export', true ) != 'save_export' )
			return false;
		
		// check capability
		if ( ! current_user_can('manage_options' ) )
			return false;
		if ( ! current_user_can('edit_post', $this->document_id ) )
			return false;
		
		// Verify nonce
		$submitbox_key = 'e2w_doc_submitpost';
		$path_to_CMB2_class = E2w_Export2word::plugin_dir_path() . 'vendor/webdevstudios/cmb2/includes/CMB2.php';
		$nonce = sanitize_html_class( 'nonce_' . basename( $path_to_CMB2_class ) . $submitbox_key );			
	
		if ( ! isset( $_POST[$nonce] ) || empty( $_POST[$nonce] ) || ! wp_verify_nonce( $_POST[$nonce], $nonce ) )
			return false;
		
		if ( ! isset( $_REQUEST[$nonce] ) || empty( $_REQUEST[$nonce] ) || ! check_admin_referer( $nonce, $nonce ) )
			return false;
			
		return true;
	
	}	
	
	private function initialize(){

		if ( ! $this->load_dependencies() )
			return false;
		
		// create a new PhpWord Object			// http://phpword.codeplex.com/documentation
		$this->PhpWord = new \PhpOffice\PhpWord\PhpWord();
		
		// create writer
		$this->writer = \PhpOffice\PhpWord\IOFactory::createWriter(  $this->PhpWord, 'Word2007' );		
		
		// create document
		if ( ! $this->create_document() )
			return false;
		
		// export document
		if ( ! $this->save_to_temp_file() )
			return false;
		
		if ( ! $this->download_del_temp_file() )
			return false;
		
		// // finished, everything done, do something
		// do_action( "e2w_finished_{$this->args['blabla']}", $this->args );		
		
		return true;
	}
	
	private function load_dependencies(){
		// Include the PhpWord through composer autoload
		$autoload = E2w_Export2word::plugin_dir_path() . 'vendor/autoload.php';
		if( ! file_exists( $autoload ) ) {
			return false; 
		} else {
			require_once( $autoload );
			return true;
		}		
	}
	
	private function create_document(){
		
		// ??? ckeck for errors
		
		// // ???
		// // Set document properties (i.e. creator, company, description, title, etc...)
		// $this->set_doc_properties( $post );
		
		// // ???
		// // Set default document styles
		// $this->set_doc_styles();		
		
		// get tree_data_arr
		$e2w_doc_tree_properties = get_post_meta( $this->document_id, 'e2w_doc_tree_properties', true );
		$tree_data_arr = strlen( $e2w_doc_tree_properties ) > 0 ? json_decode( $e2w_doc_tree_properties, true)[0] : null ;
		
		if ( $tree_data_arr === null )
			return false;
		
		// convert tree_data_arr to document_structure
		$document_structure = self::tree_data_arr_to_document_structure( $tree_data_arr['children'] );
		
		// create sections
		foreach ( $document_structure as $document_structure_section ) {
			if( ! $this->create_document_section( $document_structure_section ) )
				return false;
		}
		
		return true;
		
	}
	
	private function create_document_section( $document_structure_section, $queried_obj = null, $section_style = null ){
		
		// get section_type
		if ( array_key_exists( 'section_type', $document_structure_section ) && !empty( $document_structure_section['section_type'] ) ) {
			$section_type = $document_structure_section['section_type'];
		} else {
			// ??? check if node is root ....wirklich???
			return false;
		}
		
		// get queried_obj_type
		$queried_obj_type = array_key_exists( 'queried_obj_type', $document_structure_section ) && !empty( $document_structure_section['queried_obj_type'] ) ? $document_structure_section['queried_obj_type'] : 'none';
		
		switch ( $section_type ){
			case 'query':

				// get query_type
				if ( array_key_exists( 'query_type', $document_structure_section ) && !empty( $document_structure_section['query_type'] ) ) {
					$query_type = $document_structure_section['query_type'];
				} else {
					return;
				}
				
				// get query_args
				if ( array_key_exists( 'query_args', $document_structure_section ) && !empty( $document_structure_section['query_args'] ) ){
					$query_builder = new E2w_Query_Builder( $queried_obj, json_decode( $document_structure_section['query_args'], true ) );
					$query_args = $query_builder->get_query_args();
				}
				if ( !isset( $query_args ) || empty( $query_args ) )
					return;
				
				// get queried objects
				switch ( $query_type ){
					case 'query_post':
						$query = new WP_Query( $query_args );
						$queried_objs = $query->get_posts();
						break;
					case 'query_term':
						$query = new WP_Term_Query( $query_args );
						$queried_objs = $query->get_terms();
						break;
					case 'query_user':
						$query = new WP_User_Query( $query_args );
						$queried_objs = $query->get_users();
						break;
				}
				
				break;
			case 'singular':
				// ???			
				break;
			case 'table_of_content':
				// ???
				break;
		}
		
		// get section style if not inherited				
		if ( $section_style === null ){
			$section_style = array();
			// get style page_break
			if ( array_key_exists( 'page_break', $document_structure_section ) && !empty( $document_structure_section['page_break'] ) ) {
				$section_style['breakType'] = $document_structure_section['page_break'];
			}
			// get style page_break
			if ( array_key_exists( 'orientation', $document_structure_section ) && !empty( $document_structure_section['orientation'] ) ) {
				$section_style['orientation'] = $document_structure_section['orientation'];
			}
		}
		
		// create new Section
		$section = $this->PhpWord->createSection( $section_style );
		
		// get html
		if ( array_key_exists( 'template_id', $document_structure_section ) && is_numeric( $document_structure_section['template_id'] ) ) {
			// get template
			$template_post_obj = get_post( $document_structure_section['template_id'] );
			$template = get_post_meta( $template_post_obj->ID, 'e2w_tmpl_content', true );
			// process template
			$template_processor = new E2w_Template_Processor( $queried_obj_type, $queried_obj,  $template );
			$html = wpautop( $template_processor->process_template() );			
		} else {
			$html = '';
		}
		
		// add html to section
		\PhpOffice\PhpWord\Shared\E2w_Html::addHtml( $section, $html );
		
		// recursive
		if ( array_key_exists( 'children', $document_structure_section ) && !empty( $document_structure_section['children'] ) ) {
			if ( !isset( $queried_objs ) || empty( $queried_objs ) ) {
				// create section for each child
				foreach ( $document_structure_section['children'] as $child ) {
					$this->create_document_section( $child, null, $section_style );
				}
			} else {
				// if query, loop objects
				foreach ( $queried_objs as $queried_obj ) {
					// and create section for each child
					foreach ( $document_structure_section['children'] as $child ) {
						$this->create_document_section( $child, $queried_obj, $section_style );
					}
				}
			}
		}
		
		return true;
	}
	
	private static function tree_data_arr_to_document_structure( $tree_nodes ){
		$sections = array();
		
		// ???
		
		foreach ( (array) $tree_nodes as $tree_node ){
			
			$sections[$tree_node['id']] = array(
				'node_id'	=> $tree_node['id'],
				'title'	=> $tree_node['text'],
			);

			foreach ( $tree_node['data']['jsform'] as $key => $val ){
				$sections[$tree_node['id']][$key] = $val;
			}
			
			$sections[$tree_node['id']]['children'] = self::tree_data_arr_to_document_structure( $tree_node['children'] );
			
		}
		
		return $sections;
	}
	
	private function save_to_temp_file(){
		
		try {
			// create tempFile
			$this->temp_file_uri = tempnam('', 'e2w_');
			// save writer to tempFile
			$this->writer->save( $this->temp_file_uri );
		} catch ( Exception $e ) {
			return false;
		}		
		
		return true;
	}
	
	private function download_del_temp_file(){

		if ( ! file_exists( $this->temp_file_uri ) || ! is_file( $this->temp_file_uri ) )
			return false;
		
		// die ($this->temp_file_uri);
		$this->filename = date('Y_m_d_H_i') . '_e2w_output.docx';
		
		// download tempFile
		try {
			header('Content-Type: application/octet-stream');
			header('Content-Description: File Transfer');
			header('Content-Length: ' . filesize( $this->temp_file_uri ));
			header('Content-Disposition: attachment; filename=' . $this->filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			flush();
			readfile( $this->temp_file_uri );
			
			// delete tempFile	
			unlink( $this->temp_file_uri );
			
			exit;
			
		} catch ( Exception $e ) {
			return false;
		}
		
		
		return true;
	}
	
	
	
	
	/*
	private function set_doc_properties($post) {
		$properties = $this->php_word->getDocInfo();
		$properties->setCreator(get_the_author());
		$properties->setCompany(get_bloginfo('name')); // change to make it a dynamic setting
		$properties->setTitle(htmlspecialchars($post->post_title));
		$properties->setDescription(htmlspecialchars($post->post_excerpt));
	}	
	*/
	
	/*
	private function set_doc_styles() {
		// Default Title Styles
		$this->php_word->addParagraphStyle('TitleStyle', array('spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(24)));
		$title_style = array('color' => '000', 'size' => 18, 'bold' => true);
		$this->php_word->addTitleStyle(1, $title_style, 'TitleStyle');
		// Default Paragraph Styles
		$this->php_word->setDefaultParagraphStyle(
			array(
				'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12)
			)
		);
	}	
	*/
	
}


?>