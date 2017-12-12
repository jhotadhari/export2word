jQuery( document ).ready( function( $ ) {
	
	var radioSelector = '#cmb2-metabox-e2w_submit input[type=radio][name=e2w_save_export]';
	var $submitBtn = $('input#submit');
	
	// on init
	function init(){
		var label = $( 'label[for=' + $( radioSelector + ':checked').attr('id') + ']' ).html();
		changeLabel(label);
	}
	init();
	
	// on change
    $(radioSelector).change(function() {
        var label = $( 'label[for=' + this.id + ']' ).first().html();
        changeLabel(label);
    });
    
	function changeLabel(label){
		$submitBtn.val(label);
	}    
	
});