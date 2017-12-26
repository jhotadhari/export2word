<?


// wrapper function to init E2w_Exporter
function e2w_export_on_save_post( $post_id = null ) {
	if ( $post_id === null )
		return;
	
	// If this is just a revision, return
    if ( get_post_status( $post_id ) === 'auto-draft' )
    	return;
	
	if ( wp_is_post_revision( $post_id ) )
		return;
	
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return;
	
	// check if post_type is e2w_document
	if ( get_post_type( $post_id ) != 'e2w_document' )
		return;
	
	if ( get_post_meta( $post_id, 'e2w_doc_save_export', true ) != 'save_export' )
		return;	
	
	// include and init Exporter
	E2w_export2word::include_dir(  E2w_export2word::plugin_dir_path() . 'inc/exporter/' );
	new E2w_Exporter( $post_id );

}
// add_action( 'save_post_e2w_document', 'e2w_export_on_save_post', 100 );
add_action( 'save_post', 'e2w_export_on_save_post', 100 );

?>