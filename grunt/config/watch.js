module.exports = {
	// watch task does the same like build...
	
	root_files: {
		files: [
			'src/root_files/**/*',
			'!src/root_files/<%= global["pkg"].name %>.php',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'copy:root_files',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
	plugin_main_file: {
		files: [
			'src/root_files/<%= global["pkg"].name %>.php',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'string-replace:plugin_main_file',	// copies plugin_main_file to destination
			'concat:plugin_main_file',		// add banner plugin_main_file
			'local_sync:<%= local_sync.wp_install %>'
		]
	},	
	
	
	
	
	
	vendor: {
		files: [
			'vendor/**/*',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'copy:vendor',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
	images: {
		files: [
			'src/images/**/*',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'copy:images',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
	fonts: {
		files: [
			'src/fonts/**/*',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'copy:fonts',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
	readme: {
		files: [
			'src/readme/**/*',
			'!**/dont_touch/**/*',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'concat:readme',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
	
	
	inc: {
		files: [
			'src/inc/**/*',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'string-replace:inc_to_dest',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	
		
	// assets
	js: {
		files: [
				'src/js/**/*.js',
				'<%= pattern.global_exclude %>',
		],
		tasks: [
			'jshint',
			'uglify:main',
			'local_sync:<%= local_sync.wp_install %>:<%= local_sync.version %>'
		]
	},
	
	styles: {
		files: [
			'src/sass/**/*.scss',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'sass:main',
			'local_sync:<%= local_sync.wp_install %>'
		]
	},
	

	
	potomo_pos: {
		files: [
			'src/languages/**/*.po',
			'<%= pattern.global_exclude %>',
		],
		tasks: [
			'potomo:main',
			'local_sync:<%= local_sync.wp_install %>'
		]
	}
};