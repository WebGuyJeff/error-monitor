import PropTypes from 'prop-types'
import styles from './Header.module.scss'

const Header = ( {
	pluginName,
	pluginDescription,
	status
} ) => {
	const lastScan = status.lastScan ? `🔍 Last scan: ${status.lastScan}` : '🔍 Last scan: Never'
	const lastLog = status.lastLog ? `📝 Last log: ${status.lastLog}` : '📃 Last log: Never'
	const emailStatus = status.emailConfigured ? '✅ Email configured' : '❌ Email not configured'
	const cronStatus = status.cronScheduled ? '✅ Scan scheduled' : '❌ Scan not scheduled'

	return (
		<header className={styles.header}>

			<div className={styles.title}>
				<span
					className="dashicons-webguyjeff-logo"
					style={{ fontSize: '2em', marginRight: '0.2em' }}
				/>

				<div>
					<h1>{pluginName}</h1>
					{pluginDescription && (
						<p>{pluginDescription}</p>
					)}
				</div>
			</div>

			<hr className={styles.divider} />

			<div className={styles.status}>
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
	pluginDescription: PropTypes.string,

	status: PropTypes.shape( {
		lastScan: PropTypes.string,
		lastLog: PropTypes.string,
		emailConfigured: PropTypes.bool,
		cronScheduled: PropTypes.bool,
	} ).isRequired,
}

export { Header }
