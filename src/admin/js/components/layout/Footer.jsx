import PropTypes from 'prop-types'

const Footer = ( { pluginName } ) => {
	return (
		<div className="errorMonitor__footer">
			<div className="errorMonitor__footer-inner">
				<div className="errorMonitor__footer-left">
					<strong>{pluginName}</strong>{' '}
					<span className="errorMonitor__footer-meta">
						by{' '}
						<a
							href="https://webguyjeff.com"
							target="_blank"
							rel="noopener noreferrer"
						>
							Web Guy Jeff
						</a>
					</span>
				</div>

				<div className="errorMonitor__footer-right">
					<a
						href="https://github.com/WebGuyJeff/error-monitor"
						target="_blank"
						rel="noopener noreferrer"
					>
						GitHub Repo
					</a>

					<a
						className="coffeeButton"
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
	pluginName: PropTypes.string.isRequired
}

export { Footer }
