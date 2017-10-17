module.exports = {
	
	vendor: {		
		expand: true,
		cwd: 'vendor/',
		src: ['**/*', '<%= pattern.global_exclude %>'],
		dest: '<%= dest_path %>/vendor/'	
	},
	
	images: {		
		expand: true,
		cwd: 'src/images/',
		src: ['**/*', '<%= pattern.global_exclude %>'],
		dest: '<%= dest_path %>/images/'	
	},
	
	fonts: {		
		expand: true,
		cwd: 'src/fonts/',
		src: ['**/*', '<%= pattern.global_exclude %>'],
		dest: '<%= dest_path %>/fonts/'	
	},
	
};