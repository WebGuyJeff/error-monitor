const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const wordpressConfig = require('@wordpress/scripts/config/webpack.config')

module.exports = {
	...wordpressConfig,
	entry: {
		'admin/css/error-monitor-admin': './src/admin/admin.scss.js',
		'admin/js/error-monitor-admin': './src/admin/admin.js',
	},
	plugins: [
		...wordpressConfig.plugins,
		new BrowserSyncPlugin({
			proxy: 'localhost:6969', // Live WordPress site. Using IP breaks it.
			ui: { port: 3001 }, // BrowserSync UI.
			port: 3000, // Dev port on localhost.
			logLevel: 'debug',
			reload: false, // false = webpack handles reloads.
			browser: 'google-chrome-stable',
			files: [
				'src/**',
				'classes/**',
				'patterns/**',
				'parts/**',
				'templates/**',
				'**/**.json',
			],
		}),
	],
}
