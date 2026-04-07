import { createElement, useRef, useEffect } from '@wordpress/element'

const LogsTab = ({ logsHTML, fetchLogs, loadingAction }) => {
	const logRef = useRef(null)

	useEffect(() => {
		if (logRef.current) {
			logRef.current.scrollTop = logRef.current.scrollHeight
		}
	}, [logsHTML])

	return createElement(
		'div',
		{ className: 'adminPage_container fullWidth' },
		createElement('h2', null, 'Log Viewer'),
		createElement('p', null, 'Browse stored logs from the database.'),
		createElement('hr'),
		createElement(
			'div',
			{ className: 'errorMonitor__logViewer' },
			createElement(
				'div',
				{ className: 'adminButtonRow' },
				createElement(
					'button',
					{ type: 'button', className: 'button button-secondary', disabled: loadingAction === 'fetch_grouped', onClick: () => fetchLogs('grouped') },
					'Grouped View',
				),
				createElement(
					'button',
					{ type: 'button', className: 'button button-secondary', disabled: loadingAction === 'fetch_raw', onClick: () => fetchLogs('raw') },
					'Raw View',
				),
			),
			createElement('div', {
				ref: logRef,
				className: 'errorMonitor__logOutput',
				dangerouslySetInnerHTML: { __html: logsHTML || '<div><p>No logs found.</p></div>' },
			}),
			createElement('p', { style: { fontSize: '12px', color: '#777' } }, 'Only the last 100 occurrences per log are stored.'),
		),
	)
}

export { LogsTab }
