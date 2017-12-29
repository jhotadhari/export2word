module.exports = {
	
	readme: {
		options: {	
			banner: '=== <%= global["pkg"].fullName %> ===\nTags: <%= global["pkg"].tags %>\nDonate link: <%= global["pkg"].donateLink %>\nContributors: <%= global["pkg"].contributors %>\nTested up to: <%= global["pkg"].wpVersionTested %>\nRequires at least: <%= global["pkg"].wpRequiresAtLeast%>\nRequires PHP: <%= global["pkg"].phpRequiresAtLeast%>\nStable tag: trunk\nLicense: <%= global["pkg"].license %>\nLicense URI: <%= global["pkg"].licenseUri %>\n\n<%= global["pkg"].description %>\n\n\n',
			footer: '\n\n== Changelog ==\n\n<%= changelog %>'
		},
		src: [
			'src/readme/readme.txt'
		],
		dest: '<%= dest_path %>/readme.txt'
	},
		
	plugin_main_file: {
		options: {
			banner: '<?php \n/*\nPlugin Name: <%= global["pkg"].fullName %>\nPlugin URI: <%= global["pkg"].uri %>\nDescription: <%= global["pkg"].description %>\nVersion: <%= global["pkg"].version %>\nAuthor: <%= global["pkg"].author %>\nAuthor URI: <%= global["pkg"].authorUri %>\nLicense: <%= global["pkg"].license %>\nLicense URI: <%= global["pkg"].licenseUri %>\nText Domain: <%= global["pkg"].textDomain %>\nDomain Path: <%= global["pkg"].domainPath %>\nTags: <%= global["pkg"].tags %>\n*/\n\n?>'
		},
		src: [
			'<%= dest_path %>/<%= global["pkg"].name %>.php'
		],
		dest: '<%= dest_path %>/<%= global["pkg"].name %>.php'
	}
	
};