import PropTypes from 'prop-types'
import { useRef, useEffect, useState } from '@wordpress/element'
import { Panel, Card } from '../components/layout'
import { ButtonRow, Button } from '../components/controls'

const LogsTab = ( {
	logsHTML,
	handleFetchLogs,
	loadingAction,
	logView
} ) => {
	const logRef = useRef( null )

	// activeFetchLogs is a fix for button:disabled flash on load.
	const [ activeFetchLogs, setActiveFetchLogs ] = useState( null )
	const handleLogViewClick = ( view ) => {
		setActiveFetchLogs( view )
		handleFetchLogs( view )
	}

	useEffect( () => {
		if ( !loadingAction ) {
			setActiveFetchLogs( null )
		}
	}, [ loadingAction ] )

	useEffect( () => {
		if ( logRef.current ) {
			logRef.current.scrollTop = logRef.current.scrollHeight
		}
	}, [ logsHTML ] )

	return (
		<Panel>
			<Card>
				<h2>Log Browser</h2>

				<p>Browse stored logs from the database.</p>

				<hr />

				<div className="errorMonitor__logViewer">

					<ButtonRow layout="conjoined">
						<Button
							label={'Grouped'}
							variant={logView === 'grouped' ? 'primary' : 'secondary'}
							disabled={
								activeFetchLogs === 'grouped' &&
								loadingAction === 'fetch_grouped'
							}
							onClick={ () => handleLogViewClick( 'grouped' ) }
						/>
						<Button
							label={'Raw'}
							variant={logView === 'raw' ? 'primary' : 'secondary'}
							disabled={
								activeFetchLogs === 'raw' &&
								loadingAction === 'fetch_raw'
							}
							onClick={ () => handleLogViewClick( 'raw' ) }
						/>
					</ButtonRow>

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
			</Card>
		</Panel>
	)
}

LogsTab.propTypes = {
	logsHTML: PropTypes.string,
	handleFetchLogs: PropTypes.func.isRequired,
	loadingAction: PropTypes.string,
	logView: PropTypes.string
}

export { LogsTab }
