import PropTypes from 'prop-types'
import { Panel, Card } from '../components/layout'
import { TextInput } from '../components/fields'
import { ButtonRow, Button } from '../components/controls'
import { useSettingBinder } from '../../hooks'

const LogFileTab = ( {
	settingsState,
	debouncedUpdateSetting,
	flushUpdateSetting,
	discoverLog,
	status,
	toggleDebug,
	invalidField,
	loadingAction
} ) => {

	const bindSetting = useSettingBinder( {
		settingsState,
		debouncedUpdateSetting,
		flushUpdateSetting,
		invalidField,
	} )

	return (
		<Panel layout="columns">
			<Card>

				<form>

					<h2>Log File Status</h2>

					<table className="widefat striped">
						<thead>
							<tr>
								<th colSpan={2}>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Path</th>
								<td>{ status.path || 'Not set' }</td>
							</tr>
							<tr>
								<th>Source</th>
								<td>{ status.source || 'none' }</td>
							</tr>
							<tr>
								<th>Exists</th>
								<td>{ status.exists ? 'Yes' : 'No' }</td>
							</tr>
							<tr>
								<th>Readable</th>
								<td>{ status.readable ? 'Yes' : 'No' }</td>
							</tr>
						</tbody>
					</table>

					<TextInput
						{ ...bindSetting( 'log_file_path' ) }
						label="Log File Path"
						classes="field-large"
						description="Configure the path to the error log file."
					/>

					<ButtonRow>
						<Button
							label={ loadingAction === 'discover_log' ? 'Discovering...' : 'Auto Discover Log File' }
							variant="secondary"
							disabled={ loadingAction === 'discover_log' }
							onClick={ discoverLog }
						/>
					</ButtonRow>

				</form>

			</Card>
			<Card>

				<form>

					<h2>WP Debug Settings</h2>

					<label>
						<input
							type="checkbox"
							checked={ !!status.wp_debug }
							onChange={ ( event ) => toggleDebug( 'wp_debug', event.target.checked ) }
						/>
						{' '}WP_DEBUG
					</label>

					<br />

					<label>
						<input
							type="checkbox"
							checked={ !!status.wp_debug_log }
							onChange={ ( event ) => toggleDebug( 'wp_debug_log', event.target.checked ) }
						/>
						{' '}WP_DEBUG_LOG
					</label>

					<br />

					<label>
						<input
							type="checkbox"
							checked={ !!status.wp_debug_display }
							onChange={ ( event ) => toggleDebug( 'wp_debug_display', event.target.checked ) }
						/>
						{' '}WP_DEBUG_DISPLAY
					</label>

				</form>

			</Card>
		</Panel>
	)
}

LogFileTab.propTypes = {
	settingsState: PropTypes.objectOf(
		PropTypes.oneOfType( [ PropTypes.string, PropTypes.number, PropTypes.bool ] )
	).isRequired,

	debouncedUpdateSetting: PropTypes.func.isRequired,
	flushUpdateSetting: PropTypes.func.isRequired,
	discoverLog: PropTypes.func.isRequired,
	toggleDebug: PropTypes.func.isRequired,

	status: PropTypes.shape( {
		path: PropTypes.string,
		source: PropTypes.string,
		exists: PropTypes.bool,
		readable: PropTypes.bool,
		wp_debug: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.number ] ),
		wp_debug_log: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.number ] ),
		wp_debug_display: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.number ] ),
	} ).isRequired,

	invalidField: PropTypes.string,
	loadingAction: PropTypes.string,
}

export { LogFileTab }
