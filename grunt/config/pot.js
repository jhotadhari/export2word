module.exports = {
	options:{
		text_domain: '<%= global["pkg"].textDomain %>',
		msgmerge: false,	// true will merge it into existing po file, but with fuzzy translations
		dest: 'src/languages/',
		keywords: ['__','_e','esc_html__','esc_html_e','esc_attr__', 'esc_attr_e', 'esc_attr_x', 'esc_html_x', 'ngettext', '_n', '_ex', '_nx' ],
	},
	files:{
	src:  [
		'src/**/*.php',
		'<%= pattern.global_exclude %>',
	],
	expand: true,
	}
};