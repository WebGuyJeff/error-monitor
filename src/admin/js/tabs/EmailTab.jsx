import PropTypes from 'prop-types'
import { useMemo } from '@wordpress/element'
import { TextInput, SelectInput } from '../components/fields'
import { useSettingBinder } from '../hooks/use-setting-binder'

const isEmailConfigured = ( state ) => {
	const required = [ 'host', 'port', 'username', 'password', 'from_email', 'to_email' ]

	return required.every( ( key ) => !!state[ key ] )
}

const EmailTab = ( {
	settingsState,
	updateSetting,
	debouncedUpdateSetting,
	flushUpdateSetting,
	runTest,
	smtpOutput,
	invalidField,
	loadingAction
} ) => {
	const enableTests = useMemo( () => isEmailConfigured( settingsState ), [ settingsState ] )

	const bindSetting = useSettingBinder( {
		settingsState,
		updateSetting,
		debouncedUpdateSetting,
		flushUpdateSetting,
		invalidField,
	} )

	return (
		<div className="adminPage_container">
			<h2>SMTP Settings</h2>

			<TextInput
				{ ...bindSetting( 'username' ) }
				label="Username"
				classes="field-medium"
			/>

			<TextInput
				{ ...bindSetting( 'password' ) }
				label="Password"
				type="password"
				classes="field-medium"
			/>

			<TextInput
				{ ...bindSetting( 'host' ) }
				label="Host"
				classes="field-medium"
			/>

			<SelectInput
				{ ...bindSetting( 'port', { mode: 'instant' } ) }
				label="Port"
				classes="field-small"
				value={ `${settingsState.port ?? '587'}` }
				options={ { 25: '25', 465: '465', 587: '587', 2525: '2525' } }
			/>

			<hr />

			<h2>Message Sending</h2>

			<ul className="adminInstructionsList">
				<li>
					The <code>sent from</code> email should match your website domain to improve deliverability.
				</li>
				<li>
					Ensure DNS is configured with <strong>DMARC</strong>, <strong>SPF</strong>, and <strong>DKIM</strong> so the{' '}
					<code>sent from</code> domain can be authenticated to improve deliverability.
				</li>
			</ul>

			<TextInput
				{ ...bindSetting( 'from_email' ) }
				label="Sent-from email address"
				classes="field-medium"
			/>

			<TextInput
				{ ...bindSetting( 'to_email' ) }
				label="Email to send notifications to"
				classes="field-medium"
			/>

			<h2>Test Settings</h2>

			<p>Save your settings before testing connection and sending a test email.</p>

			<div className="errorMonitor__testWrapper">
				<div className="adminButtonRow">
					<button
						type="button"
						className="button button-secondary"
						disabled={ !enableTests || loadingAction === 'test_smtp' }
						onClick={ () => runTest( 'smtp' ) }
					>
						{ loadingAction === 'test_smtp' ? 'Testing Connection...' : 'Test Connection' }
					</button>

					<button
						type="button"
						className="button button-secondary"
						disabled={ !enableTests || loadingAction === 'test_email' }
						onClick={ () => runTest( 'email' ) }
					>
						{ loadingAction === 'test_email' ? 'Sending Test Email...' : 'Send Test Email' }
					</button>
				</div>

				<div
					id="errorMonitor__consoleOutput"
					className="errorMonitor__logOutput"
					style={ { display: smtpOutput.length ? 'block' : 'none' } }
				>
					{ smtpOutput.map( ( line, index ) => (
						<p
							key={ `${line}-${index}` }
							className={ `errorMonitor__alert${line.status ? ` errorMonitor__alert-${line.status}` : ''}` }
						>
							{ line.message }
						</p>
					) ) }
				</div>
			</div>
		</div>
	)
}

EmailTab.propTypes = {
	settingsState: PropTypes.objectOf(
		PropTypes.oneOfType( [ PropTypes.string, PropTypes.number, PropTypes.bool ] )
	).isRequired,

	updateSetting: PropTypes.func.isRequired,
	debouncedUpdateSetting: PropTypes.func.isRequired,
	flushUpdateSetting: PropTypes.func.isRequired,
	runTest: PropTypes.func.isRequired,

	smtpOutput: PropTypes.arrayOf(
		PropTypes.shape( {
			message: PropTypes.string.isRequired,
			status: PropTypes.string,
		} )
	).isRequired,

	invalidField: PropTypes.string,
	loadingAction: PropTypes.string,
}

export { EmailTab }
