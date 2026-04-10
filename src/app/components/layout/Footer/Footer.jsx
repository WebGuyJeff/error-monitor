import PropTypes from 'prop-types'
import styles from './Footer.module.scss'

const Footer = ( {
	pluginName,
	PluginURI,
	PluginVersion,
	AuthorName,
	AuthorURI
} ) => {

	return (
		<div className={styles.footer}>
			<div className={styles.inner}>
				<div className={styles.left}>
					<strong>{pluginName}</strong>{' '}
					{AuthorName && AuthorURI && (
						<span className={styles.meta}>
							by{' '}
							<a
								href={AuthorURI}
								target="_blank"
								rel="noopener noreferrer"
							>
								{AuthorName}
							</a>
						</span>
					)}
					{PluginVersion && (
						<>
							<br />
							<span className={styles.meta}>v{PluginVersion}</span>
						</>
					)}
				</div>

				<div className={styles.right}>

					{PluginURI && (
						<a
							href={PluginURI}
							target="_blank"
							rel="noopener noreferrer"
						>
							GitHub
						</a>
					)}

					<a
						className={styles.coffeeButton}
						href="https://www.buymeacoffee.com/webguyjeff"
						target="_blank"
						rel="noopener noreferrer"
					>
						<img
							src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png"
							alt="Buy Me a Coffee"
							style={{
								height: '60px',
								width: '217px',
							}}
						/>
					</a>
				</div>
			</div>
		</div>
	)
}

Footer.propTypes = {
	pluginName: PropTypes.string.isRequired,
	PluginURI: PropTypes.string,
	PluginVersion: PropTypes.string,
	AuthorName: PropTypes.string,
	AuthorURI: PropTypes.string
}

export { Footer }
