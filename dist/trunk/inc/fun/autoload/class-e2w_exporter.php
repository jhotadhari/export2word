<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class E2w_Exporter {
	
	protected $PhpWord;
	protected $filename;
	protected $args;
	
	function __construct( $document_id = null, $args = array() ) {
		// stop if no post_id is given
		if ( $document_id === null )
			return;
		
		// parse args
		$defaults = array(
			// 'blala' => 'is doch egal',
		);	
		$this->args = wp_parse_args( $args, $defaults );
		
		// Include the PhpWord through composer autoload, create a new PhpWord Object			// http://phpword.codeplex.com/documentation
		include_once( WP_PLUGIN_DIR . '/export2word/' . 'vendor/autoload.php' );
		$this->PhpWord = new \PhpOffice\PhpWord\PhpWord();
		
		// create document
		$this->create_document( $document_id );
		
		// export document
		$this->filename = date('Y_m_d_H_i') . '_e2w_output.docx';
		$this->save_download_del_file_temp();
		
		// // finished, everything done, do something
		// do_action( "e2w_finished_{$args['blabla']}", $args );
		
	}
	
	protected function create_document( $document_id ){
		$document = get_post( $document_id );
		
		// get tree_data_arr
		$tree_data_arr = json_decode( get_post_meta( $document->ID, 'e2w_doc_tree_properties', true ), true)[0];
		
		// convert tree_data_arr to document_structure
		$document_structure = self::tree_data_arr_to_document_structure( $tree_data_arr['children'] );
		
		// create sections
		foreach ( $document_structure as $document_structure_section ) {
			$this->create_document_section( $document_structure_section );
		}
		
	}
	
	protected function create_document_section( $document_structure_section, $queried_obj = null, $section_style = null ){
		
		// get section_type
		if ( array_key_exists( 'section_type', $document_structure_section ) && !empty( $document_structure_section['section_type'] ) ) {
			$section_type = $document_structure_section['section_type'];
		} else {
			return;
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
	
	}
	
	protected static function tree_data_arr_to_document_structure( $tree_nodes ){
		$sections = array();
		
		foreach ( $tree_nodes as $tree_node ){
			
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
	
	protected function save_download_del_file_temp(){
		// create writer
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter(  $this->PhpWord, 'Word2007' );
		// create tempFile
		$temp_file_uri = tempnam('', 'e2w_');
		
		// save writer to tempFile
		$objWriter->save( $temp_file_uri );
		
		// download tempFile
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $this->filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize( $temp_file_uri ));
		flush();
		readfile($temp_file_uri);
		
		// delete tempFile	
		unlink($temp_file_uri);
	
	}
	
	
	
	
}

// wrapper function to init E2w_Exporter
function e2w_export_on_save_post( $post_id ) {
	
	// If this is just a revision, return
	if ( wp_is_post_revision( $post_id ) )
		return;
	
	// check if post_type is e2w_document
	if ( get_post_type( $post_id ) != 'e2w_document' )
		return;
	
	// check if document should be exported
	if ( get_post_meta( $post_id, 'e2w_doc_save_export', true ) === 'save_export' )
		new E2w_Exporter( $post_id );

}
add_action( 'save_post', 'e2w_export_on_save_post', 100 );

?>