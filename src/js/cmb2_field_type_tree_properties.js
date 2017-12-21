jQuery( document ).ready( function( $ ) {
	
	function isJSON( str ) {
		if ( /^\s*$/.test( str ) ) return false;
		str = str.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@');
		str = str.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
		str = str.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
		return (/^[\],:{}\s]*$/).test(str);
	}

	function get_tree_data( string ){
		
		var stringSample = '[{"text":"Document","icon":true,"li_attr":{"id":"j1_1","class":"no_dragging"},"a_attr":{"href":"#","id":"j1_1_anchor"},"state":{"loaded":true,"opened":true,"selected":true,"disabled":true},"data":{},"children":[{"id":"j1_2","text":"sample section","icon":true,"li_attr":{"id":"j1_2"},"a_attr":{"href":"#","id":"j1_2_anchor"},"state":{"loaded":true,"opened":false,"selected":false,"disabled":false},"data":{},"children":[]}]}]';
		
		if ( 'undefined' === typeof( string ) || string.length === 0 ){
			return $.parseJSON( stringSample );
		}
		
		if ( ! isJSON( string ) ){
			return stringSample;
		}
		
		try {
			return $.parseJSON( string );
		} catch (e) {
			return $.parseJSON( stringSample );
		}
		
	}
		
	// config tree
	var treeConf = {
		plugins: [
			'contextmenu',
			'wholerow',
			'dnd',	// Drag & drop
		],
		dnd:{
			is_draggable: function (nodes) {
				var i = 0, j = nodes.length;
				for(; i < j; i++) {
				   if(this.get_node(nodes[i], true).hasClass('no_dragging')) {
					   return false;
				   }
				}
				return true;
			}
		},
		contextmenu:{
			items: tree_node_contextmenu_item_cb
		},
		core: {
			check_callback: true,
			contextmenu: true
		} 		
	};
	
	// config jsform
	var jsformConf = {
		 trackChanges: true
	};
	
	
	

	
	
	// init tree properties field
	function init_tree_properties_field(){
		$('.cmb-type-tree-properties .cmb2-tree-properties-wrapper').each(function(){
			
			var $this = $( this );
			
			var $tree = $this.children('.jstree.jstree-wrapper');
			
			var $properties = $this.children('.node-properties.node-properties-wrapper');
			var $properties_selectedNodeTitle = $properties.find('.selected-node-name');
			var $properties_selectedNodeId = $properties.children('.selected-node-id');
			var $properties_jsform = $properties.children('.jsform.jsform-wrapper');
			
			var $fieldDataInput = $this.find( '.tree-properties-data' );
			
			// init Form
			$properties_jsform.jsForm({
				config: jsformConf,
				data: {},
				conditionals: {
					section_type___query: function ( ele, data ) {
						conditionalShow( ele, data, this.func.name );
					},
					section_type___singular: function ( ele, data ) {
						conditionalShow( ele, data, this.func.name );
					},
					query_input___json: function ( ele, data ) {
						conditionalShow( ele, data, this.func.name );
					},
					inherit_style___no: function ( ele, data ) {
						conditionalShow( ele, data, this.func.name );
					},
				}
			});
	
			function conditionalShow( ele, data, functionName ) {
				
				var depend_value =  /___\S+/i.exec( functionName )[0]; // .replace('___', '');
				var depend = functionName.replace( depend_value, '');
				depend_value =  depend_value.replace('___', '');
				
				// ??? check if dependency is not hidden

				
				
				// show or hide conditional fields
				if ( data[depend] == depend_value ){
					$(ele).show();
				} else {
					$(ele).hide();
				}
				
			}		
	
			
			
			
			// apply form conditions
			$properties_jsform.find('.e2w-data-type').on( 'click', function (e) {
				var obj = $properties_jsform.jsForm('get');
				$properties_jsform.jsForm('fill', obj);
			});
			
			
			// get treeData from treeDataInput element
			treeConf.core.data = get_tree_data( $fieldDataInput.val() );
			
			// init tree
			var instance = $tree
				.on( 'changed.jstree rename_node.jstree update_cell.jstree-grid' , function () {
					update_treeDataInput();
					update_propertiesBox();
				})
				.on( 'init.jstree deselect_all.jstree select_node.jstree' , function () {
					update_propertiesBox();
				})
				.jstree( treeConf );
				

				
			// update treeDataInput element
			function update_treeDataInput(){
				var instance = $tree.jstree(true);
				var data_json_string = JSON.stringify( instance.get_json(), null, ' ');
				$fieldDataInput.val( data_json_string ).change();
			}
		
			// update properties box
			function update_propertiesBox(){
				var instance = $tree.jstree(true);
				var selectedNodeId = instance.get_selected()[0];
				var selectedNode = null;
				var selectedNodeText = '';
				
				if ( selectedNodeId != undefined ) {
					if ( selectedNodeId === 'j1_1' ) {
						instance.deselect_all();
					} else {
						selectedNode = instance.get_node(selectedNodeId);
						selectedNodeText = selectedNode.text;
					}			
				}
				
				// update titleField
				$properties_selectedNodeTitle.html( '<h3>' + selectedNodeText + '</h3>' );
				// update hidden field (node id)
				$properties_selectedNodeId.val( selectedNodeId );
				
				// update tree from form
				$properties_jsform.on( 'keyup click', function () {
						
					var formData = $properties_jsform.jsForm( 'get' );
					
					// update tree node data
					var node = $tree.jstree(true).get_node( $properties_selectedNodeId.val() );
					
					if ( node != null ){
						if ( node.data === null ){
							node.data = {};
						}
						if ( node.data.jsform === null ){
							node.data.jsform = {};
						}					
						node.data.jsform = formData;
					}
					
					update_treeDataInput();	
				});		

				// update form from tree
				if ( selectedNode === null) {
					$properties_jsform.jsForm('reset');
					$properties_jsform.hide();					
				} else if ( selectedNode != null && selectedNode.data != null && selectedNode.data.jsform != null) {
					$properties_jsform.jsForm('reset');
					$properties_jsform.jsForm( 'fill', selectedNode.data.jsform );
					$properties_jsform.show();
				} else {
					$properties_jsform.jsForm('reset');
					$properties_jsform.show();
				}
					
			}
			
		});
	
	}
	
	
	
	

	
	
	
	
	// init on page load
	init_tree_properties_field();

	// init on cmb2_add_row
	var cmb = window.CMB2;
	cmb.metabox().on('cmb2_add_row', function( e ) {
		init_tree_properties_field();
	});

	
	
	
	
	
	// show desc on input hover	
	$('.show-on-input-hover').hide();
	$('.hover-show-desc').each( function() {
		var classes = $(this).attr('class');
		var label_class = /input-for-\S+/i.exec( classes )[0].replace('input-for-', 'desc-for-');
		
		$(this).hover(
			function() {
				$( '.' + label_class ).show();
			}, function() {
				$( '.' + label_class ).hide();
			}
		);
		
	});
	
	
	
	
	

	// more or less a copy of the original, but will not apply different menu to root node
	function tree_node_contextmenu_item_cb(o, cb) {
		if (o.parents.length < 2) {
			return {
				"create" : {
					"separator_before"	: false,
					"separator_after"	: true,
					"_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
					"label"				: "Create",
					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						inst.create_node(obj, {}, "last", function (new_node) {
							setTimeout(function () { inst.edit(new_node); },0);
						});
					}
				}
			};
		} else {
			return {
				"create" : {
					"separator_before"	: false,
					"separator_after"	: true,
					"_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
					"label"				: "Create",
					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						inst.create_node(obj, {}, "last", function (new_node) {
							setTimeout(function () { inst.edit(new_node); },0);
						});
					}
				},
				"rename" : {
					"separator_before"	: false,
					"separator_after"	: false,
					"_disabled"			: false, //(this.check("rename_node", data.reference, this.get_parent(data.reference), "")),
					"label"				: "Rename",
					"shortcut"			: 113,

					"shortcut_label"	: "F2",

					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						inst.edit(obj);
					}
				},
				"remove" : {
					"separator_before"	: false,
					"icon"				: false,
					"separator_after"	: false,
					"_disabled"			: false, //(this.check("delete_node", data.reference, this.get_parent(data.reference), "")),
					"label"				: "Delete",
					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						if(inst.is_selected(obj)) {
							inst.delete_node(inst.get_selected());
						}
						else {
							inst.delete_node(obj);
						}
					}
				},
				// "ccp" : {
				// 	"separator_before"	: true,
				// 	"icon"				: false,
				// 	"separator_after"	: false,
				// 	"label"				: "Edit",
				// 	"action"			: false,
				// 	"submenu" : {
				// 		"cut" : {
				// 			"separator_before"	: false,
				// 			"separator_after"	: false,
				// 			"label"				: "Cut",
				// 			"action"			: function (data) {
				// 				var inst = $.jstree.reference(data.reference),
				// 					obj = inst.get_node(data.reference);
				// 				if(inst.is_selected(obj)) {
				// 					inst.cut(inst.get_top_selected());
				// 				}
				// 				else {
				// 					inst.cut(obj);
				// 				}
				// 			}
				// 		},
				// 		"copy" : {
				// 			"separator_before"	: false,
				// 			"icon"				: false,
				// 			"separator_after"	: false,
				// 			"label"				: "Copy",
				// 			"action"			: function (data) {
				// 				var inst = $.jstree.reference(data.reference),
				// 					obj = inst.get_node(data.reference);
				// 				if(inst.is_selected(obj)) {
				// 					inst.copy(inst.get_top_selected());
				// 				}
				// 				else {
				// 					inst.copy(obj);
				// 				}
				// 			}
				// 		},
				// 		"paste" : {
				// 			"separator_before"	: false,
				// 			"icon"				: false,
				// 			"_disabled"			: function (data) {
				// 				return !$.jstree.reference(data.reference).can_paste();
				// 			},
				// 			"separator_after"	: false,
				// 			"label"				: "Paste",
				// 			"action"			: function (data) {
				// 				var inst = $.jstree.reference(data.reference),
				// 					obj = inst.get_node(data.reference);
				// 				inst.paste(obj);
				// 			}
				// 		}
				// 	}
				// }
			};
		}
	}

	
});