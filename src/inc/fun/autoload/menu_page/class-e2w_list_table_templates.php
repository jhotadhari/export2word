<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class E2w_List_Table_Templates extends WP_List_Table {
	
	protected static $total_items = 10;

	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Template', 'export2word' ),
			'plural'   => __( 'Templates', 'export2word' ),
			'ajax'     => false,
		) );

	}
	
	public function no_items() {
	  _e( 'No templates avaliable.', 'export2word' );
	}
	
	protected function get_views(){
		$views = array();
		$current = ( !empty( $_REQUEST['viewvar'] ) && gettype( $_REQUEST['viewvar'] ) === 'string' ? esc_attr( $_REQUEST['viewvar'] ) : 'all');
		
		// All link
		$all_url = remove_query_arg('viewvar');
		$class = ($current == 'all' ? ' class="current"' :'');
		$views['all'] = "<a href='{$all_url }' {$class} >" . __('All', 'export2word') . '</a>';
		
		// trash
		$trash_url = add_query_arg('viewvar','trash');
		$class = ($current == 'trash' ? ' class="current"' :'');
		$views['trash'] = "<a href='{$trash_url}' {$class} >" . __('Trash', 'export2word') . '</a>';		
		
		return $views;
	}
	
	function column_title( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.
		$nonce = wp_create_nonce( 'e2w_nonce_template' );
		$view = ( !empty($_REQUEST['viewvar']) && gettype( $_REQUEST['viewvar'] ) === 'string' ? esc_attr( $_REQUEST['viewvar'] ) : 'all');
			
		$actions = array();
		
		switch( $view ) {
			case 'all':
				
				$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $item['ID'] ) ), esc_attr__( 'Edit', 'export2word' ) );
					
				$trash_query_args = array(
					'page'   => $page,
					'tab'   => 'templates',
					'action' => 'trash',
					'viewvar' => $view,
					'_wpnonce' => $nonce,
					'e2w_template'  => absint( $item['ID'] ),
				);					
				
				$actions['trash'] = 
					sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( add_query_arg( $trash_query_args, 'admin.php' ) ),
						_x( 'Trash', 'List table row action', 'export2word' )
					);				
				breaK;
				
			case 'trash':
				
				$restore_query_args = array(
					'page'   => $page,
					'tab'   => 'templates',
					'action' => 'restore',
					'viewvar' => $view,
					'_wpnonce' => $nonce,
					'e2w_template'  => absint( $item['ID'] ),
				);
				
				$actions['restore'] =
					sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( add_query_arg( $restore_query_args, 'admin.php' ) ),
						_x( 'Restore', 'List table row action', 'export2word' )
					);
									
				$delete_query_args = array(
					'page'   => $page,
					'tab'   => 'templates',
					'action' => 'delete',
					'viewvar' => $view,
					'_wpnonce' => $nonce,
					'e2w_template'  => absint( $item['ID'] ),
				);				
				
				$actions['delete'] =
					sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( add_query_arg( $delete_query_args, 'admin.php' ) ),
						_x( 'Delete Permanently', 'List table row action', 'export2word' )
					);
					
				break;
		}
		
		return $item['title'] . $this->row_actions( $actions );
	}
	
		
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb( $item ) {
	  return sprintf(
		'<input type="checkbox" name="bulk[]" value="%s" />', $item['ID']
	  );
	}
	
	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'title'   => __( 'Title', 'export2word' ),
			'debug'    => __( 'Debug', 'export2word' ),
		);
		
		return $columns;
	}
	
	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
		);
		
		return $sortable_columns;
	}
	
	public function get_bulk_actions() {
		$view = ( !empty($_REQUEST['viewvar']) && gettype( $_REQUEST['viewvar'] ) === 'string' ? esc_attr( $_REQUEST['viewvar'] ) : 'all');
				
		$actions = array();
		
		switch( $view ) {
			case 'all':
				$actions['bulk-trash'] = __( 'Trash', 'export2word' );
				break;
			case 'trash':
				$actions['bulk-delete'] = __( 'Delete Permanently', 'export2word' );
				$actions['bulk-restore'] = __( 'Restore', 'export2word' );
				break;
		}
		
		
		return $actions;
	}
	
	protected function get_items( $per_page = null, $current_page = null, $s = null ){
		
		$orderby = !empty( $_REQUEST['orderby'] ) && gettype( $_REQUEST['orderby'] ) === 'string' ? esc_attr( $_REQUEST['orderby'] ) : 'ID';
		$order = !empty( $_REQUEST['order'] ) && gettype( $_REQUEST['order'] ) === 'string' ? esc_attr( $_REQUEST['order'] ) : 'DESC';
	
		$args = array(
			'post_type'    => 'e2w_template',
			'orderby'      => $orderby,
			'order'        => $order,
		);
		
		if ( isset($s) ){
			$args['s'] = $s;
		}
		
		if ( isset($per_page) && isset($current_page) ){
			$args['posts_per_page'] = $per_page;
			$args['offset'] = ( $current_page - 1 ) * $per_page;
		}
		
		if ( !empty( $_REQUEST['viewvar'] && gettype( $_REQUEST['viewvar'] ) === 'string' ) ){
			switch ( esc_attr( $_REQUEST['viewvar'] ) ){
				case 'trash':
					$args['post_status'] = 'trash';
					break;
			}
		}
		
		// The Query
		$query = new WP_Query( $args );
		$items = array();
		
		// The Loop
		while ( $query->have_posts() ) {
			$query->the_post();
			
			$items[get_the_id()] = array(
				'ID' => get_the_id(),
				'title' => get_the_title(),
			);
		}
		
		// Restore original Post Data 
		wp_reset_postdata();
		
		return $items;
		
	}
	
	public function prepare_items() {
	
		$this->_column_headers = $this->get_column_info();
		
		$this->process_bulk_action();
		
		$per_page     = $this->get_items_per_page( 'templates_per_page', 5 );
		$current_page = $this->get_pagenum();
		$s = isset( $_REQUEST['s'] ) && gettype( $_REQUEST['s'] ) === 'string' ? esc_attr( $_REQUEST['s'] ) : null;
		
		$items = $this->get_items( $per_page, $current_page, $s );	
		
		$total_items = count($this->get_items( null, null, $s ));
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );
		
		$this->items = $items;
	}
	
	public function process_bulk_action() {
		
		$view = !empty( $_REQUEST['viewvar'] ) && gettype( $_REQUEST['viewvar'] ) === 'string' ? esc_attr( $_REQUEST['viewvar'] ) : 'all';
		
		$action = $this->current_action();
		
		if ( isset( $action ) && !empty( $action ) ) {
			if ( strpos( $action, 'bulk-' ) === 0 && is_numeric( strpos( $action, 'bulk-' ) ) ){
				$this->process_bulk_action_bulk( $action );
			} else {
				$this->process_bulk_action_single( $action );
			}
		}
		
	}
	
	protected function process_bulk_action_single( $action ) {
		
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );
		
		if ( ! wp_verify_nonce( $nonce, 'e2w_nonce_template' ) ) {
			wp_die( 'Nope! Security check failed!' );
		} else {
			
			$post_id = is_numeric( $_GET['e2w_template'] ) ? absint( $_GET['e2w_template'] ) : null;
			if ( $post_id === null )
				return;
			
			switch( $action ){
				case 'trash':
						wp_trash_post( $post_id );
					break;
				case 'delete':
						wp_delete_post( $post_id );
					break;
				case 'restore':
						wp_untrash_post( $post_id );
					break;					
				default:
					// silence ...
			}
			
		}
		
	}
	
	protected function process_bulk_action_bulk( $action ) {
		
		if ( !isset( $_POST['action'] ) ){
			if ( $_POST['action'] != $action ) {
				return;
			}
		}
		
		if ( !isset( $_POST['action2'] ) ){
			if ( $_POST['action2'] != $action ) {
				return;
			}
		}
		
		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ){
				wp_die( 'Nope! Security check failed!' );
			} else {
				$template_ids = esc_sql( $_POST['bulk'] );
				if ( $template_ids ){
					
					switch( $action ){
						
						case 'bulk-trash':
							foreach ( $template_ids as $template_id ) {
								wp_trash_post( $template_id );
							}
							break;
							
						case 'bulk-delete':
							foreach ( $template_ids as $template_id ) {
								wp_delete_post( $template_id );
							}
							break;
						case 'bulk-restore':
							foreach ( $template_ids as $template_id ) {
								wp_untrash_post( $template_id );
							}
							break;							
						default:
							// silence ...
					}
					
				}
			
			}
		}
		
	}
	
}



?>