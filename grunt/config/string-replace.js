'use strict';

function getReplacements() {

	var replacements = [];
	Object.keys( global["pkg"] ).forEach( function( key ) {
		replacements.push( {
			pattern: new RegExp( 'taskRunner_set_' + key, 'g'),
			replacement: global["pkg"][key]
		} );
	});
	
	return replacements;
	
}

module.exports = {
	options: {
		replacements: getReplacements()
	},
	
	// will replace string and copy plugin_main_file to destination
	plugin_main_file: {
		files: {'<%= dest_path %>/<%= global["pkg"].name %>.php':'src/root_files/<%= global["pkg"].name %>.php'}
	},
	
	// will replace string and update file in source. should only run on dist
	inc_update_src: {
		options: {
			replacements: [{
				pattern: /taskRunner_set_version/g,
				replacement:  '<%= global["pkg"].version %>'
			}]			
		},
		files: [{
			expand: true,
			cwd: 'src/inc/',
			src: ['**/*.php','<%= pattern.global_exclude %>'],
			dest: 'src/inc/'
		}],
	},	
	
	// will replace and copy inc to destination
	inc_to_dest: {
		files: [{
			expand: true,
			cwd: 'src/inc/',
			src: ['**/*.php','<%= pattern.global_exclude %>'],
			dest: '<%= dest_path %>/inc/'
		}],
	},	
};