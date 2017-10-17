module.exports = function(grunt){
	
	// _setPaths
	// used by build.js
	// used by dist.js
	grunt.registerTask('_setPaths', 'sub task', function(process) {
		
		// set paths
		var i;
		var dest_path;
		var pkg = grunt.file.readJSON("package.json");
		
		grunt.log.writeln('version: ' + pkg.version);

		if ( process == 'build' ) {
			dest_path = [
				'test'
			];
		} else if ( process == 'dist' ) {
			dest_path = [
				'dist' + '/tags/' + pkg.version,
				'dist' + require('path').sep + 'trunk'
			];
		}
		
		// run tasks
		for ( i = 0, len = dest_path.length; i < len; i++) {
			grunt.task.run('_tasks:' + dest_path[i] + ':' + process);
		}
		
	});
};