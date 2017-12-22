<?php


/**
 * Register a e2w_template post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function e2w_add_post_type_e2w_template() {

	$labels = array(
		'name'                  => _x( 'Templates', 'Post Type General Name', 'export2word' ),
		'singular_name'         => _x( 'Template', 'Post Type Singular Name', 'export2word' ),
		'menu_name'             => __( 'Templates', 'export2word' ),
		'name_admin_bar'        => __( 'Template', 'export2word' ),
		'archives'              => __( 'Templates', 'export2word' ),
		'attributes'            => __( 'Template Attributes', 'export2word' ),
		'parent_item_colon'     => __( 'Parent Template:', 'export2word' ),
		'all_items'             => __( 'All Templates', 'export2word' ),
		'add_new_item'          => __( 'Export2Word: Add New Template', 'export2word' ),
		'add_new'               => __( 'Add New', 'export2word' ),
		'new_item'              => __( 'New Template', 'export2word' ),
		'edit_item'             => __( 'Export2Word: Edit Template', 'export2word' ),
		'update_item'           => __( 'Update Template', 'export2word' ),
		'view_item'             => __( 'View Template', 'export2word' ),
		'view_items'            => __( 'View Templates', 'export2word' ),
		'search_items'          => __( 'Search Template', 'export2word' ),
		'not_found'             => __( 'Not found', 'export2word' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'export2word' ),
		'featured_image'        => __( 'Featured Image', 'export2word' ),
		'set_featured_image'    => __( 'Set featured image', 'export2word' ),
		'remove_featured_image' => __( 'Remove featured image', 'export2word' ),
		'use_featured_image'    => __( 'Use as featured image', 'export2word' ),
		'insert_into_item'      => __( 'Insert into Template', 'export2word' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Template', 'export2word' ),
		'items_list'            => __( 'Templates list', 'export2word' ),
		'items_list_navigation' => __( 'Templates list navigation', 'export2word' ),
		'filter_items_list'     => __( 'Filter Templates list', 'export2word' ),
	);
	
	$args = array(
		'label'                 => __( 'Template', 'export2word' ),
		'description'           => __( 'Template description', 'export2word' ),
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
	register_post_type( 'e2w_template', $args );

}
add_action( 'init', 'e2w_add_post_type_e2w_template' );
add_action( 'e2w_on_activate_before_flush', 'e2w_add_post_type_e2w_template' );

?>