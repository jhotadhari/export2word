module.exports = {
	add: {
		options: {
			A: true,
		},
	},
	commit: {
		options: {
			m: 'v<%= global["pkg"].version %>\n\n<%= commit_msg %>'
		}
	},	
	tag: {
		options: {
			a: ['v<%= global["pkg"].version %>'],
			m: ['<%= commit_msg %>']
		}
	},
};