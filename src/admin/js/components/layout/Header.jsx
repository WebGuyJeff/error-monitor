import PropTypes from 'prop-types'

const Header = ( { pluginName, status } ) => {
	const lastScan = status.lastScan ? `🔍 Last scan: ${status.lastScan}` : '🔍 Last scan: Never'
	const lastLog = status.lastLog ? `📝 Last log: ${status.lastLog}` : '📃 Last log: Never'
	const emailStatus = status.emailConfigured ? '✅ Email configured' : '❌ Email not configured'
	const cronStatus = status.cronScheduled ? '✅ Scan scheduled' : '❌ Scan not scheduled'

	return (
		<header className="adminHeader">
			<hr style={{ display: 'none' }} className="wp-header-end" />

			<div className="adminTitle">
				<span
					className="dashicons-webguyjeff-logo"
					style={{ fontSize: '2em', marginRight: '0.2em' }}
				/>

				<div>
					<h1>{pluginName}</h1>
					<p>Get notified about new errors on your WordPress site</p>
				</div>
			</div>

			<hr className="adminHeaderDivider" />

			<div className="pluginStatus">
				<span>{lastScan}</span>
				<span>{lastLog}</span>

				<div>
					<span>{emailStatus}</span>
					<span>{cronStatus}</span>
				</div>
			</div>
		</header>
	)
}

Header.propTypes = {
	pluginName: PropTypes.string.isRequired,

	status: PropTypes.shape( {
		lastScan: PropTypes.string,
		lastLog: PropTypes.string,
		emailConfigured: PropTypes.bool,
		cronScheduled: PropTypes.bool,
	} ).isRequired,
}

export { Header }
