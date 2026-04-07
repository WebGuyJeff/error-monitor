import { createElement } from '@wordpress/element'

const Header = ( { pluginName, status } ) => {
	const lastScan = status.lastScan ? `🔍 Last scan: ${status.lastScan}` : '🔍 Last scan: Never'
	const lastLog = status.lastLog ? `📝 Last log: ${status.lastLog}` : '📃 Last log: Never'
	const emailStatus = status.emailConfigured ? '✅ Email configured' : '❌ Email not configured'
	const cronStatus = status.cronScheduled ? '✅ Scan scheduled' : '❌ Scan not scheduled'

	return createElement(
		'header',
		{ className: 'adminHeader' },
		createElement( 'hr', { style: { display: 'none' }, className: 'wp-header-end' } ),
		createElement(
			'div',
			{ className: 'adminTitle' },
			createElement( 'span', { className: 'dashicons-webguyjeff-logo', style: { fontSize: '2em', marginRight: '0.2em' } } ),
			createElement(
				'div',
				null,
				createElement( 'h1', null, pluginName ),
				createElement( 'p', null, 'Get notified about new errors on your WordPress site' ),
			),
		),
		createElement( 'hr', { className: 'adminHeaderDivider' } ),
		createElement(
			'div',
			{ className: 'pluginStatus' },
			createElement( 'span', null, lastScan ),
			createElement( 'span', null, lastLog ),
			createElement( 'div', null, createElement( 'span', null, emailStatus ), createElement( 'span', null, cronStatus ) ),
		),
	)
}

export { Header }
