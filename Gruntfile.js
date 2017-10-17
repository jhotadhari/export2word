'use strict';

module.exports = function(grunt){
	// load plugins
	require('time-grunt')(grunt);
	require('load-grunt-tasks')(grunt);
	// load tasks
	grunt.loadTasks('grunt/tasks');
	// load config
	initConfigs(grunt, 'grunt/config');
};

function initConfigs(grunt, folderPath) {
				
	global['dest_path'] = 'test';
	
	var config = {
		pattern: {
			global_exclude: [
				'!*~',
				'!**/*~',
				'!_test*',
				'!_del_*',
				'!**/_del_*',
			]
		},
		pkg: "<%= global['pkg'] %>",
		wp_installs: grunt.file.readJSON("wp_installs.json"),
		dest_path:  "<%= global['dest_path'] %>",
		commit_msg: "<%= global['commit_msg'] %>",
		changelog: "<%= global['changelog'] %>",
	};
	
	global['pkg'] = grunt.file.readJSON("package.json");
	
    grunt.file.expand(folderPath + '/**/*.js').forEach(function(filePath) {
        var fileName = filePath.split('/').pop().split('.')[0];
        var fileData = require('./' + filePath);
        config[fileName] = fileData;
    });
    grunt.initConfig(config);
}