import { fetchHttpRequest } from './_fetch'
import { errorMonitorInlinedVars } from './_get-wp-inlined-vars'
import { showToast } from './_toast'
import { consoleOutput } from './_console-output'

const { restActionURL, restNonce } = errorMonitorInlinedVars

/**
 * Send action to backend.
 */
const sendAction = async (action, payload = {}, field = null) => {
	const isSmtpTest = (action === 'test' && payload.type === 'smtp') || false

	if (isSmtpTest) {
		consoleOutput('Connecting...')
	}

	const result = await fetchHttpRequest(restActionURL, {
		method: 'POST',
		headers: {
			'X-WP-Nonce': restNonce,
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({ action, payload }),
	})

	// Reset validation state
	if (field) {
		field.classList.remove('em-invalid')
		field.removeAttribute('aria-invalid')
	}

	if (isSmtpTest) {
		if (!result.ok) {
			consoleOutput(result.output, 'danger')
			result.output[0] = 'SMTP test failed'
		} else {
			consoleOutput(result.output, 'success')
			result.output[0] = 'SMTP test success'
		}
	}

	if (!result.ok) {
		showToast(result.output[0], 'danger')

		if (result.data?.field && field) {
			field.classList.add('em-invalid')
			field.setAttribute('aria-invalid', 'true')
		}

		return result
	}

	showToast(result.output[0], 'success')

	return result
}

export { sendAction }
