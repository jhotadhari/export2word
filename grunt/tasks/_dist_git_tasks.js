module.exports = function(grunt){
	
	// _dist_git_tasks
	// used by	_setPaths
	grunt.registerTask('_dist_git_tasks', 'sub task', function() {

		grunt.task.run([
			'git:add',
			'git:commit',
			'git:tag',
		]);

	});
};