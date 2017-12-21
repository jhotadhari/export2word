jQuery( document ).ready( function( $ ) {
	
	var radioSelector = '#cmb2-metabox-e2w_doc_submit input[type=radio][name=e2w_doc_save_export]';
	
	// on init
	function init(){
		var label = $( 'label[for=' + $( radioSelector + ':checked').attr('id') + ']' ).html();
		changeLabel($( radioSelector + ':checked').attr('id'));
	}
	init();
	
	// on change
    $(radioSelector).change(function() {
        var label = $( 'label[for=' + this.id + ']' ).first().html();
        changeLabel(label);
    });
    
	function changeLabel(label){
		$('#cmb2-metabox-e2w_doc_submit input#submit').val(label);
	}    
	
});