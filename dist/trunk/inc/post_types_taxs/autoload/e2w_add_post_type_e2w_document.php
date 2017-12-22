<?php


/**
 * Register a document post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function e2w_add_post_type_e2w_document() {

	$labels = array(
		'name'                  => _x( 'Documents', 'Post Type General Name', 'export2word' ),
		'singular_name'         => _x( 'Document', 'Post Type Singular Name', 'export2word' ),
		'menu_name'             => __( 'Documents', 'export2word' ),
		'name_admin_bar'        => __( 'Document', 'export2word' ),
		'archives'              => __( 'Documents', 'export2word' ),
		'attributes'            => __( 'Document Attributes', 'export2word' ),
		'parent_item_colon'     => __( 'Parent Document:', 'export2word' ),
		'all_items'             => __( 'All Documents', 'export2word' ),
		'add_new_item'          => __( 'Export2Word: Add New Document', 'export2word' ),
		'add_new'               => __( 'Add New', 'export2word' ),
		'new_item'              => __( 'New Document', 'export2word' ),
		'edit_item'             => __( 'Export2Word: Edit Document', 'export2word' ),
		'update_item'           => __( 'Update Document', 'export2word' ),
		'view_item'             => __( 'View Document', 'export2word' ),
		'view_items'            => __( 'View Documents', 'export2word' ),
		'search_items'          => __( 'Search Document', 'export2word' ),
		'not_found'             => __( 'Not found', 'export2word' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'export2word' ),
		'featured_image'        => __( 'Featured Image', 'export2word' ),
		'set_featured_image'    => __( 'Set featured image', 'export2word' ),
		'remove_featured_image' => __( 'Remove featured image', 'export2word' ),
		'use_featured_image'    => __( 'Use as featured image', 'export2word' ),
		'insert_into_item'      => __( 'Insert into Document', 'export2word' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Document', 'export2word' ),
		'items_list'            => __( 'Documents list', 'export2word' ),
		'items_list_navigation' => __( 'Documents list navigation', 'export2word' ),
		'filter_items_list'     => __( 'Filter Documents list', 'export2word' ),
	);
	
	$args = array(
		'label'                 => __( 'Document', 'export2word' ),
		'description'           => __( 'Document description', 'export2word' ),
		'labels'                => $labels,
		'supports'              => array('title'),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => 5,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'show_in_rest'          => false,
		'menu_icon'             => null,	// https://developer.wordpress.org/resource/dashicons/#admin-page
		'capability_type'       => 'post',
	);
	register_post_type( 'e2w_document', $args );

}
add_action( 'init', 'e2w_add_post_type_e2w_document' );
add_action( 'e2w_on_activate_before_flush', 'e2w_add_post_type_e2w_document' );

?>