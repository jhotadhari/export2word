module.exports = {
	options: {
		// args: ["--verbose"],
		// exclude: [".git*","node_modules"],
		recursive: true
	},
	local_sync: {
		options: {
			src: '<%= local_sync.src %>',
			dest: '<%= local_sync.dest %>',
			delete: true                               
		}
	}
};