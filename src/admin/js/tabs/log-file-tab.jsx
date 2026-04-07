import { createElement } from '@wordpress/element'
import { TextInput } from '../components/fields'

const LogFileTab = ({ settingsState, updateSetting, discoverLog, status, toggleDebug, loadingAction }) =>
	createElement(
		'div',
		{ className: 'adminPage_container' },
		createElement('h2', null, 'Log File'),
		createElement(
			'table',
			{ className: 'widefat striped' },
			createElement('thead', null, createElement('tr', null, createElement('th', { colSpan: 2 }, 'Status'))),
			createElement(
				'tbody',
				null,
				createElement('tr', null, createElement('th', null, 'Path'), createElement('td', null, status.path || 'Not set')),
				createElement('tr', null, createElement('th', null, 'Source'), createElement('td', null, status.source || 'none')),
				createElement('tr', null, createElement('th', null, 'Exists'), createElement('td', null, status.exists ? 'Yes' : 'No')),
				createElement('tr', null, createElement('th', null, 'Readable'), createElement('td', null, status.readable ? 'Yes' : 'No')),
			),
		),
		createElement(TextInput, {
			label: 'Log File Path',
			description: 'Configure the path to the error log file.',
			value: settingsState.log_file_path ?? '',
			onChange: (event) => updateSetting('log_file_path', event.target.value),
		}),
		createElement(
			'div',
			{ className: 'adminButtonRow' },
			createElement(
				'button',
				{ type: 'button', className: 'button button-secondary', disabled: loadingAction === 'discover_log', onClick: discoverLog },
				loadingAction === 'discover_log' ? 'Discovering...' : 'Auto Discover Log File',
			),
		),
		createElement('hr'),
		createElement('h2', null, 'Debug Configuration'),
		createElement(
			'label',
			null,
			createElement('input', { type: 'checkbox', checked: !!status.wp_debug, onChange: (event) => toggleDebug('wp_debug', event.target.checked) }),
			' WP_DEBUG',
		),
		createElement('br'),
		createElement(
			'label',
			null,
			createElement('input', { type: 'checkbox', checked: !!status.wp_debug_log, onChange: (event) => toggleDebug('wp_debug_log', event.target.checked) }),
			' WP_DEBUG_LOG',
		),
		createElement('br'),
		createElement(
			'label',
			null,
			createElement('input', { type: 'checkbox', checked: !!status.wp_debug_display, onChange: (event) => toggleDebug('wp_debug_display', event.target.checked) }),
			' WP_DEBUG_DISPLAY',
		),
	)

export { LogFileTab }
