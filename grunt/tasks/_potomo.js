module.exports = function(grunt){
	
	// _potomo
	// used by	_tasks.js
	grunt.registerTask('_potomo', 'sub task', function( _task ) {

		if ( ! _task) {
			var _task = 'main';
		}
		
		var dir = grunt.config.get('potomo')[_task].files[0].cwd;
		var filePattern = grunt.config.get('potomo')[_task].files[0].src[0];
		
		if ( grunt.file.expand( dir + '**/' + filePattern ).length ) {
			grunt.task.run(['potomo:' + _task ]);
		}
		
	});
};