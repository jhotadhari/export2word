module.exports = function(grunt){

	// _tasks
	// used by	_setPaths.js
	grunt.registerTask('_tasks', 'sub task', function(dest_path, process) {
		
		var pkg = grunt.file.readJSON("package.json");
		
		global['dest_path'] = dest_path;
		
		grunt.task.run([
			// clean up dest folder
			'clean',
			
			//	composer
			'composer:update',
			
			// readme
			'concat:readme',
			
			'string-replace:plugin_main_file',	// copies plugin_main_file to destination
			'concat:plugin_main_file',		// add banner plugin_main_file
			'string-replace:inc_to_dest',	// copies inc to destination
			'copy',
				
			// js
			'jshint',
			'uglify:main',
			
			// style
			'sass:main',
			
			// potomo
			'pot',
			'_potomo',
			
		]);
		
	});
};