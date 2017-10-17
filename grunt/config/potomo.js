module.exports = {
	options: {
		poDel: false
	},			
	main: { 
		files: [{
			expand: true,
			cwd: 'src/languages/',
			src: ['*.po'],
			dest: '<%= dest_path %>/languages',
			ext: '.mo',
			nonull: true
		}]				
	},
};