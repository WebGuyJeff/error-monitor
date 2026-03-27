import { fetchHttpRequest } from './_fetch'
import { cleanOutput, addAlerts } from './_alert-inline'
import { errorMonitorInlinedVars } from './_get-wp-inlined-vars'
import { formLock } from './_form-lock'

/**
 * Handle the submission.
 *
 * Send the request and display test feedback.
 *
 * @param {SubmitEvent} event
 *
 */
async function testRequest(event) {
	event.preventDefault()

	const form = event.currentTarget.closest('form')
	const testName = event.currentTarget.dataset.test

	if (!form || !testName) return

	let body = ''
	if (testName === 'smtp') {
		body = JSON.stringify({ test: 'smtp' })
	} else if (testName === 'email') {
		body = JSON.stringify({ test: 'email' })
	}

	// Fetch params.
	const { restTestURL, restNonce } = errorMonitorInlinedVars

	const fetchOptions = {
		method: 'POST',
		headers: {
			'X-WP-Nonce': restNonce,
			'Content-Type': 'application/json',
			Accept: 'application/json',
		},
		body: body,
	}

	try {
		formLock(form, true)
		cleanOutput(form),
			addAlerts(form, [{ text: 'Connecting...', type: 'info' }])
		let result = await fetchHttpRequest(restTestURL, fetchOptions)

		// Display post-fetch alerts.
		const postFetchAlerts = []
		result.output.forEach((message) =>
			postFetchAlerts.push({
				text: message,
				type: result.ok ? 'success' : 'danger',
			}),
		)
		addAlerts(form, postFetchAlerts)
		formLock(form, false)
	} catch (error) {
		console.error(error)
		addAlerts(form, [
			{
				text: 'Failed to test your SMTP settings due to an unknown error.',
				type: 'danger',
			},
		])
		formLock(form, false)
	}
}

export { testRequest }
