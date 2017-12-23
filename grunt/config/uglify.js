module.exports = {
	ugyly: {
		options: {
			mangle: true,
			compress: true,
			beautify: false,
		},		
		files: [{
			expand: true,
			cwd: 'src/js',
			src: '**/*.js',
			dest: '<%= dest_path %>/js',
			rename: function (dst, src) {
				return dst + '/' + src.replace('.js', '.min.js').replace('noLint/', '');
			}			
		}]
	},
	beauty: {
		options: {
			mangle: false,
			compress: false,
			beautify: true,
		},		
		files: [{
			expand: true,
			cwd: 'src/js',
			src: '**/*.js',
			dest: '<%= dest_path %>/js',
			rename: function (dst, src) {
				return dst + '/' + src.replace('noLint/', '');
			}			
		}]
	},	

};