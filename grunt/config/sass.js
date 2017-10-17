module.exports = {
	options: {
		require: [
			'susy',
			'breakpoint'
		],
		loadPath: require('node-bourbon').includePaths,
	},
	main: {
		options: {
			sourcemap: 'none',
			style: 'compressed'
		},
		files: [{
			expand: true,
			cwd: 'src/sass',
			src: ['*.scss'],
			dest: '<%= dest_path %>/css',
			ext: '.min.css'	
		}]
	}
};