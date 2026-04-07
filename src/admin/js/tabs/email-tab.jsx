import { createElement, useMemo } from '@wordpress/element'
import { TextInput, SelectInput } from '../components/fields'

const isEmailConfigured = (state) => {
	const required = ['host', 'port', 'username', 'password', 'from_email', 'to_email']
	return required.every((key) => !!state[key])
}

const EmailTab = ({ settingsState, updateSetting, runTest, smtpOutput, invalidField, loadingAction }) => {
	const enableTests = useMemo(() => isEmailConfigured(settingsState), [settingsState])

	return createElement(
		'div',
		{ className: 'adminPage_container' },
		createElement('h2', null, 'SMTP Settings'),
		createElement(TextInput, {
			label: 'Username',
			classes: 'field-medium',
			value: settingsState.username ?? '',
			onChange: (event) => updateSetting('username', event.target.value),
			invalid: invalidField === 'username',
		}),
		createElement(TextInput, {
			label: 'Password',
			type: 'password',
			classes: 'field-medium',
			value: settingsState.password ?? '',
			onChange: (event) => updateSetting('password', event.target.value),
			invalid: invalidField === 'password',
		}),
		createElement(TextInput, {
			label: 'Host',
			classes: 'field-medium',
			value: settingsState.host ?? '',
			onChange: (event) => updateSetting('host', event.target.value),
			invalid: invalidField === 'host',
		}),
		createElement(SelectInput, {
			label: 'Port',
			classes: 'field-small',
			value: `${settingsState.port ?? '587'}`,
			onChange: (event) => updateSetting('port', event.target.value),
			invalid: invalidField === 'port',
			options: { 25: '25', 465: '465', 587: '587', 2525: '2525' },
		}),
		createElement('hr'),
		createElement('h2', null, 'Message Sending'),
		createElement(
			'ul',
			{ className: 'adminInstructionsList' },
			createElement('li', null, 'The ', createElement('code', null, 'sent from'), ' email should match your website domain to improve deliverability.'),
			createElement('li', null, 'Ensure DNS is configured with ', createElement('strong', null, 'DMARC'), ', ', createElement('strong', null, 'SPF'), ', and ', createElement('strong', null, 'DKIM'), ' so the ', createElement('code', null, 'sent from'), ' domain can be authenticated to improve deliverability.'),
		),
		createElement(TextInput, {
			label: 'Sent-from email address',
			classes: 'field-medium',
			value: settingsState.from_email ?? '',
			onChange: (event) => updateSetting('from_email', event.target.value),
			invalid: invalidField === 'from_email',
		}),
		createElement(TextInput, {
			label: 'Email to send notifications to',
			classes: 'field-medium',
			value: settingsState.to_email ?? '',
			onChange: (event) => updateSetting('to_email', event.target.value),
			invalid: invalidField === 'to_email',
		}),
		createElement('h2', null, 'Test Settings'),
		createElement('p', null, 'Save your settings before testing connection and sending a test email.'),
		createElement(
			'div',
			{ className: 'errorMonitor__testWrapper' },
			createElement(
				'div',
				{ className: 'adminButtonRow' },
				createElement(
					'button',
					{ type: 'button', className: 'button button-secondary', disabled: !enableTests || loadingAction === 'test_smtp', onClick: () => runTest('smtp') },
					loadingAction === 'test_smtp' ? 'Testing Connection...' : 'Test Connection',
				),
				createElement(
					'button',
					{ type: 'button', className: 'button button-secondary', disabled: !enableTests || loadingAction === 'test_email', onClick: () => runTest('email') },
					loadingAction === 'test_email' ? 'Sending Test Email...' : 'Send Test Email',
				),
			),
			createElement(
				'div',
				{ id: 'errorMonitor__consoleOutput', className: 'errorMonitor__logOutput', style: { display: smtpOutput.length ? 'block' : 'none' } },
				smtpOutput.map((line, index) =>
					createElement('p', { key: `${line}-${index}`, className: `errorMonitor__alert${line.status ? ` errorMonitor__alert-${line.status}` : ''}` }, line.message),
				),
			),
		),
	)
}

export { EmailTab }
