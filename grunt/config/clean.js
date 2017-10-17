module.exports = {
	src: [
		// del all in dest_path
		'<%= dest_path %>/*',
		
		// skip git
		'!.gitignore',
		'!.git',
		// skip node & grunt
		'!node_modules',
		'!Gruntfile.js',
		'!package.json',
		'!pkg.json',
		'!README.md',
		// skip dir src & dist
		'!src/**',
		'!dist/**'
	]
};