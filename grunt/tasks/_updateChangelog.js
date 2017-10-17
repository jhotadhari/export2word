'use strict';
var sortObj = require('sort-object');

module.exports = function(grunt){
	
	grunt.registerTask('_updateChangelog', 'sub task', function(process) {
	
		// update global pkg (because of bump)
		global['pkg'] = grunt.file.readJSON("package.json");
			
		// get new version
		var version = global['pkg'].version;
		
		// get changelog
		var changelog = grunt.file.readJSON("changelog.json");

		var key, i, len;
		
		if ( process === 'dist' ){
			
			// set global commit_msg
			global['commit_msg'] = '';
			for ( key in changelog.next) {
				global['commit_msg'] += changelog.next[key] + '\n';
			}
			
			// update changelog obj
			changelog[version] = changelog.next;
			changelog.next = [
				''
			];
			changelog = sortObj(changelog, {sortOrder: 'desc'});
			
			// update changelog.json
			grunt.file.write('changelog.json', JSON.stringify( changelog, null, 2 ));
			
		}
		
		
		// set global changelog str, will be appended to readme.txt
		changelog = sortObj(changelog, {sortOrder: 'desc'});
		global['changelog'] = '';
		for ( key in changelog ) {
			
			if (key !== 'next'){
				global['changelog'] += key + '\n';
				
				for ( i = 0, len = changelog[key].length; i < len; i++) {
					global['changelog'] += changelog[key][i] + '\n';
				}
				
				global['changelog'] += '\n';
				
			}
			
		}
	
	});
};