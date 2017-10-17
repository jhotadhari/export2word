module.exports = {
	options: {
		replacements: [{
			pattern: /taskRunner_setVersion/g,
			replacement:  '<%= global["pkg"].version %>'
		}]
	},
	
	// will replace string and copy plugin_main_file to destination
	plugin_main_file: {
		files: {'<%= dest_path %>/<%= global["pkg"].name %>.php':'src/root_files/<%= global["pkg"].name %>.php'}
	},
	
	// will replace string and update file in source. should only run on dist
	inc_update_src: {
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