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
 * Patch every rule whose use-chain includes css-loader (plain .css, .pcss, and .scss).
 * Matching only `rule.test` strings that contain "css" misses `/\.(sc|sa)ss$/`, so
 * .module.scss never received getLocalIdent and kept hashed class names.
 */
const ruleUsesCssLoader = ( rule ) =>
	Array.isArray( rule.use ) &&
	rule.use.some(
		( loader ) =>
			typeof loader === 'object' &&
			loader.loader &&
			/[\\/]css-loader[\\/]/.test( loader.loader )
	)

const enhanceWpScriptsCssLoader = ( rule ) => {
	if ( Array.isArray( rule.oneOf ) ) {
		return { ...rule, oneOf: rule.oneOf.map( enhanceWpScriptsCssLoader ) }
	}
	if ( Array.isArray( rule.rules ) ) {
		return { ...rule, rules: rule.rules.map( enhanceWpScriptsCssLoader ) }
	}

	if ( ! ruleUsesCssLoader( rule ) ) {
		return rule
	}

	return {
		...rule,
		use: rule.use.map( ( loader ) => {
			const isCssLoader =
				typeof loader === 'object' &&
				loader.loader &&
				/[\\/]css-loader[\\/]/.test( loader.loader )

			if ( ! isCssLoader ) {
				return loader
			}

			return {
				...loader,
				options: {
					...loader.options,
					modules: {
						auto: /\.module\.(css|scss|sass|pcss)$/i,
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
