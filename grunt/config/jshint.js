module.exports = {
	all: [
		'src/js/**/*.js',
		'!src/js/**/noLint/**/*.js',
		'<%= pattern.global_exclude %>',
	]
};