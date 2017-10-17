module.exports = function(grunt){
	grunt.registerTask('local_sync', 'sync to local wp install', function( install, version ){

		var pkg, wp_installs, install, abs_path_pkg, src, dest;
		// check if args
		if ( arguments.length === 0 ){
			grunt.warn("local install must be specified");
		}
		// check if arg install is empty str
		if ( install === '' ){
			grunt.log.writeln('sync dest is empty ... no sync');
			return;
		}
		// set version 'test' if empty or undefined
		if ( version === '' || typeof version === 'undefined'){
			grunt.log.writeln('version empty or  undefined ... set to "test"');
			version = 'test';
		}
		// check if arg install is specified in wp_installs
		wp_installs = grunt.file.readJSON('wp_installs.json');
		if ( install != '' && typeof wp_installs[install] != 'object' ){
			grunt.warn("unknown local install");
		}
		
		pkg = grunt.file.readJSON('package.json');
		
		// set paths
		dest = require('path').resolve(wp_installs[install].local,pkg.name) + require('path').sep;
		
		
		if ( version === 'test' ){
			src = require('path').resolve('test') + require('path').sep;

		} else if ( version === 'trunk'){
			src = require('path').resolve('dist','trunk') + require('path').sep;

		} else if ( /((\d)\.(\d)\.(\d))/.test(version)){
			src = require('path').resolve('dist','tags',version) + require('path').sep;
			
			if (! grunt.file.exists(src)){
				grunt.warn('"' + version + '" is no valid version');
			}
		} else {
			grunt.warn('"' + version + '" is no valid version');
		}
		
		// set config
		grunt.config.merge({
			local_sync: {
				src: src,
				dest: dest,
			}
		});
		
		// run tasks
		grunt.task.run([     
			'rsync:local_sync'
		]);
		
	});
};