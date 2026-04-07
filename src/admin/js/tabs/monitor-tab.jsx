import { createElement } from '@wordpress/element'
import { TextInput, SelectInput, ToggleInput } from '../components/fields'

const MonitorTab = ( {
	settingsState,
	updateSetting,
	debouncedUpdateSetting,
	flushUpdateSetting,
	runManualScan,
	invalidField,
	loadingAction
} ) =>
	createElement(
		'div',
		{ className: 'adminPage_container' },
		createElement( 'h2', null, 'Notifications' ),
		createElement( 'p', null, 'A notification email will only be sent if new logs are found.' ),
		createElement( ToggleInput, {
			label: 'Enable scheduled monitoring',
			description: 'Disable scheduled log scanning and alerts. Manual scans will still work.',
			checked: settingsState.monitor_enabled,
			onChange: ( event ) => updateSetting( 'monitor_enabled', event.target.checked ? 1 : 0 ),
		} ),
		createElement( TextInput, {
			label: 'Scan Frequency (mins)',
			description: 'A scan will be performed at these intervals',
			type: 'number',
			classes: 'field-small',
			value: settingsState.scan_frequency_mins ?? '',
			onChange: ( event ) => debouncedUpdateSetting( 'scan_frequency_mins', event.target.value ),
			onBlur: ( event ) => flushUpdateSetting( 'scan_frequency_mins', event.target.value ),
			invalid: invalidField === 'scan_frequency_mins',
			attrs: {
				step: 1,
				min: 1,
				max: 60,
			},
		} ),
		createElement( 'hr' ),
		createElement( 'h2', null, 'Log History' ),
		createElement( SelectInput, {
			label: 'Log retention (days)',
			description: 'Logs older than the selected period will be automatically deleted to prevent database bloat.',
			classes: 'field-small',
			value: `${settingsState.log_retention_days ?? '30'}`,
			onChange: ( event ) => updateSetting( 'log_retention_days', event.target.value ),
			invalid: invalidField === 'log_retention_days',
			options: {
				7: '7 days',
				30: '30 days',
				90: '3 months',
				180: '6 months',
				365: '12 months',
			},
		} ),
		createElement( 'hr' ),
		createElement( 'h2', null, 'Manual Scan' ),
		createElement( 'p', null, 'Run a scan immediately and get emailed the results.' ),
		createElement(
			'div',
			{ className: 'adminButtonRow' },
			createElement(
				'button',
				{
					type: 'button',
					className: 'button button-primary',
					disabled: loadingAction === 'manual_scan',
					onClick: runManualScan,
				},
				loadingAction === 'manual_scan' ? 'Running Scan...' : 'Run Scan Now',
			),
		),
	)

export { MonitorTab }
