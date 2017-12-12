<?php
function cmb2_submit_render_callback( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	
	// wrapper parameters
	$button_wrapper_id = array_key_exists('button_wrapper_id', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_wrapper_id'] : 'publishing-action';
	// hidden input parameters
	$button_hidden_name_id = array_key_exists('button_hidden_name_id', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_hidden_name_id'] : 'original_publish';
	$button_hidden_value = array_key_exists('button_hidden_value', $field_type_object->field->args( 'attributes' ) ) ? $button_hidden_value = $field_type_object->field->args( 'attributes' )['button_hidden_value'] : $button_hidden_value = $_GET['action'] == 'edit' ? 'Update' : esc_attr('Publish');
	// submit button parameters
	$button_text = array_key_exists('button_text', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_text'] : null;
	$button_type = array_key_exists('button_type', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_type'] : 'primary';
	$button_name = array_key_exists('button_name', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_name'] : 'submit';
	$button_wrap = array_key_exists('button_wrap', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_wrap'] : true;
	$button_other_attributes = array_key_exists('button_other_attributes', $field_type_object->field->args( 'attributes' ) ) ?  $field_type_object->field->args( 'attributes' )['button_other_attributes'] : null;
	
	echo '<br>';
	echo '<div id="' . $button_wrapper_id . '">';
		echo '<span class="spinner"></span>';
		echo '<input name="' . $button_hidden_name_id . '" id="' . $button_hidden_name_id . '" value="' . $button_hidden_value . '" type="hidden">';
		echo get_submit_button(
			$button_text,				// $text				null
			$button_type,				// $type				'primary'
			$button_name,				// $name				'submit'
			$button_wrap,				// $wrap				true
			$button_other_attributes	// $other_attributes	null	array
		);
	echo '</div>';
	
	// hidden field. nonsence, just possibility to set data-conditional
	echo $field_type_object->input( array( 'type' => 'hidden' ) );
	// no need to echo desc, the hidden input does it already
	// echo $field_type_object->_desc( false );	
}
add_action( 'cmb2_render_submit', 'cmb2_submit_render_callback', 10, 5 );

?>