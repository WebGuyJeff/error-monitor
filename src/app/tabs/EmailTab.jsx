import PropTypes from 'prop-types'
import { useMemo } from '@wordpress/element'
import { Panel, Card } from '../components/layout'
import { TextInput, SelectInput } from '../components/fields'
import { ButtonRow, Button } from '../components/controls'
import { useSettingBinder } from '../../hooks'

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
		<Panel layout="columns">
			<Card>

				<form>

					<h2>SMTP Configuration</h2>

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

					<TextInput
						{ ...bindSetting( 'from_email' ) }
						label="Sent-from email address"
						classes="field-medium"
						description='This should match your website domain and be configured with DMARC, SPF, and DKIM to improve deliverability.'
					/>

					<TextInput
						{ ...bindSetting( 'to_email' ) }
						label="Email to send notifications to"
						classes="field-medium"
					/>

				</form>

			</Card>
			<Card>

				<h2>Test Actions</h2>

				<p>Save your settings before testing connection and sending a test email.</p>

				<ButtonRow>
					<Button
						label={ loadingAction === 'test_smtp' ? 'Testing Connection...' : 'Test Connection' }
						variant="secondary"
						disabled={ !enableTests || loadingAction === 'test_smtp' }
						onClick={ () => runTest( 'smtp' ) }
					/>
					<Button
						label={ loadingAction === 'test_email' ? 'Sending Test Email...' : 'Send Test Email' }
						variant="secondary"
						disabled={ !enableTests || loadingAction === 'test_email' }
						onClick={ () => runTest( 'email' ) }
					/>
				</ButtonRow>

				{ smtpOutput?.status &&
					<div
						className="errorMonitor__logOutput"
						style={ { display: 'block' } }
					>
						<pre
							className={ `errorMonitor__alert${smtpOutput.status ? ` errorMonitor__alert-${smtpOutput.status}` : ''}` }
						>
							{ smtpOutput.messages.join( '\n' ) }
						</pre>
					</div>
				}

			</Card>
		</Panel>
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
