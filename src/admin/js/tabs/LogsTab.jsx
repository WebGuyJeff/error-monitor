import PropTypes from 'prop-types'
import { useRef, useEffect } from '@wordpress/element'

const LogsTab = ( {
	logsHTML,
	fetchLogs,
	loadingAction
} ) => {
	const logRef = useRef( null )

	useEffect( () => {
		if ( logRef.current ) {
			logRef.current.scrollTop = logRef.current.scrollHeight
		}
	}, [ logsHTML ] )

	return (
		<div className="adminPage_container fullWidth">
			<h2>Log Viewer</h2>

			<p>Browse stored logs from the database.</p>

			<hr />

			<div className="errorMonitor__logViewer">
				<div className="adminButtonRow">
					<button
						type="button"
						className="button button-secondary"
						disabled={ loadingAction === 'fetch_grouped' }
						onClick={ () => fetchLogs( 'grouped' ) }
					>
						Grouped View
					</button>

					<button
						type="button"
						className="button button-secondary"
						disabled={ loadingAction === 'fetch_raw' }
						onClick={ () => fetchLogs( 'raw' ) }
					>
						Raw View
					</button>
				</div>

				<div
					ref={ logRef }
					className="errorMonitor__logOutput"
					dangerouslySetInnerHTML={ {
						__html: logsHTML || '<div><p>No logs found.</p></div>',
					} }
				/>

				<p style={ { fontSize: '12px', color: '#777' } }>
					Only the last 100 occurrences per log are stored.
				</p>
			</div>
		</div>
	)
}

LogsTab.propTypes = {
	logsHTML: PropTypes.string,
	fetchLogs: PropTypes.func.isRequired,
	loadingAction: PropTypes.string,
}

export { LogsTab }
