const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' )
const wordpressConfig = require( '@wordpress/scripts/config/webpack.config' )

const config = {
	...wordpressConfig,
	entry: {
		'error-monitor': './src/bootstrap.js',
	},
	plugins: [
		...wordpressConfig.plugins,
		new BrowserSyncPlugin( {
			proxy: 'localhost:6969', // Live WordPress site. Using IP breaks it.
			ui: { port: 3001 }, // BrowserSync UI.
			port: 3000, // Dev port on localhost.
			logLevel: 'debug',
			reload: false, // false = webpack handles reloads.
			browser: 'google-chrome-stable',
			files: [ 'src/**', 'classes/**', 'templates/**', 'error-monitor.php' ],
		} ),
	],
}

/**
 * Prevent CSS modules from outputting 'Button_button', and instead just output 'button'.
 */
const buildCssModuleClass = ( context, __, localName ) => {
	const prefix = 'em_'
	const file = context.resourcePath
		.split( '/' )
		.pop()
		.replace( '.module.scss', '' )

	const className = file.toLowerCase() === localName
		? prefix + localName
		: prefix + file.toLowerCase() + '_' + localName

	return className
}

/*
 * Modify existing WordPress scripts CSS loader rule instead of replacing it.
 * This rule overrides default css module hashed classes to human readable classes.
 */
const enhanceWpScriptsCssLoader = ( rule ) => {
	if ( !rule.test || !rule.test.toString().includes( 'css' ) ) {
		return rule
	}

	return {
		...rule,
		use: rule.use.map( ( loader ) => {
			const isCssLoader =
        typeof loader === 'object' &&
        loader.loader &&
        /[\\/]css-loader[\\/]/.test( loader.loader )

			if ( !isCssLoader ) return loader

			return {
				...loader,
				options: {
					...loader.options,
					modules: {
						auto: /\.module\.css$/,
						getLocalIdent: buildCssModuleClass,
					},
				},
			}
		} ),
	}
}

// ✅ apply transformation cleanly
config.module.rules = config.module.rules.map( enhanceWpScriptsCssLoader )

module.exports = config
