module.exports = {
	main: {
		files: [{
			expand: true,
			cwd: 'src/js',
			src: '**/*.js',
			dest: '<%= dest_path %>/js',
			rename: function (dst, src) {
				return dst + '/' + src.replace('.js', '.min.js');
			}			
		}]		
	}
};