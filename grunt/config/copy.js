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
	
	root_files: {		
		expand: true,
		cwd: 'src/root_files/',
		src: [
			'**/*',
			'!<%= global["pkg"].name %>.php',
			'<%= pattern.global_exclude %>'
		],
		dest: '<%= dest_path %>/'	
	},	
	
};