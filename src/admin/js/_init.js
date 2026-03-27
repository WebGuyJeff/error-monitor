import { testRequest } from './_test-request'
import { errorMonitorInlinedVars } from './_get-wp-inlined-vars'

/**
 * Initialise the email test button.
 */
const init = () => {
	const setupTestButtons = () => {
		const testButtons = [
			document.querySelector('#errorMonitor__smtpTest_button'),
			document.querySelector('#errorMonitor__emailTest_button'),
		]
		if (testButtons.length === 0) return

		testButtons.forEach((button) => {
			button.addEventListener('click', testRequest)
		})

		// Enable the submit button now that JS is ready (disabled by default).
		if (errorMonitorInlinedVars.settingsOK) {
			testButtons.forEach((button) => {
				button.disabled = false
			})
		}
	}

	// Initialise on 'doc ready'.
	const docReadyInterval = setInterval(() => {
		if (document.readyState === 'complete') {
			clearInterval(docReadyInterval)
			setupTestButtons()
		}
	}, 100)
}

export { init }
