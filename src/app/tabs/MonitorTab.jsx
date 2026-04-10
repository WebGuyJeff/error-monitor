import PropTypes from 'prop-types'
import { Panel, Card } from '../components/layout'
import { TextInput, SelectInput, ToggleInput } from '../components/fields'
import { Button, ButtonRow } from '../components/controls'
import { useSettingBinder } from '../../hooks'

const MonitorTab = ( {
	settingsState,
	updateSetting,
	debouncedUpdateSetting,
	flushUpdateSetting,
	runManualScan,
	invalidField,
	loadingAction
} ) => {

	const bindSetting = useSettingBinder( {
		settingsState,
		updateSetting,
		debouncedUpdateSetting,
		flushUpdateSetting,
		invalidField,
	} )

	return (
		<Panel layout="columns">
			<Card>

				<form>

					<h2>Monitoring Control</h2>
					<p>A notification email will only be sent if new logs are found.</p>

					<ToggleInput
						{...bindSetting( 'monitor_enabled', { type: 'toggle' } )}
						label="Enable scheduled monitoring"
						description="Manual scans will still work."
					/>

					<TextInput
						{ ...bindSetting( 'scan_frequency_mins' ) }
						label="Scan Frequency (mins)"
						description="A scan will be performed at these intervals."
						type="number"
						classes="field-small"
						attrs={ {
							step: 1,
							min: 1,
							max: 60,
						} }
					/>

					<hr />

					<h2>Log History</h2>

					<SelectInput
						{ ...bindSetting( 'log_retention_days', { mode: 'instant' } ) }
						label="Log retention (days)"
						description="Logs older than the selected period will be automatically deleted to prevent database bloat."
						classes="field-small"
						value={ `${settingsState.log_retention_days ?? '30'}` }
						options={ {
							7: '7 days',
							30: '30 days',
							90: '3 months',
							180: '6 months',
							365: '12 months',
						} }
					/>

				</form>

			</Card>

			<Card>

				<h2>Manual Actions</h2>
				<p>Run a scan immediately and get emailed the results.</p>

				<ButtonRow>
					<Button
						label={ loadingAction === 'manual_scan' ? 'Running Scan...' : 'Run Scan Now' }
						variant="primary"
						disabled={ loadingAction === 'manual_scan' }
						onClick={ runManualScan }
					/>
				</ButtonRow>

			</Card>
		</Panel>
	)
}

MonitorTab.propTypes = {
	settingsState: PropTypes.objectOf(
		PropTypes.oneOfType( [ PropTypes.string, PropTypes.number, PropTypes.bool ] )
	).isRequired,

	updateSetting: PropTypes.func.isRequired,
	debouncedUpdateSetting: PropTypes.func.isRequired,
	flushUpdateSetting: PropTypes.func.isRequired,
	runManualScan: PropTypes.func.isRequired,

	invalidField: PropTypes.string,
	loadingAction: PropTypes.string,
}

export { MonitorTab }
