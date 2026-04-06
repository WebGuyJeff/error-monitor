import { errorMonitorInlinedVars } from './_get-wp-inlined-vars'
import { sendAction } from './_actions'

// Initialise the email test buttons.
const enableTestButtons = () => {
	const testButtons = [
		document.querySelector('[data-em-test="smtp"]'),
		document.querySelector('[data-em-test="email"]'),
	]

	if (!testButtons[0] || !testButtons[1]) return

	// Enable the buttons when JS is ready (disabled by default).
	if (errorMonitorInlinedVars.settingsOK) {
		testButtons.forEach((button) => {
			button.disabled = false
		})
	}
}

// Add listeners to settings inputs.
const bindSettings = () => {
	const inputs = document.querySelectorAll('[data-em-key]')
	if (!inputs[0]) return
	inputs.forEach((input) => {
		input.addEventListener('change', (e) => {
			sendAction(
				'update_setting',
				{
					key: e.target.dataset.emKey,
					value:
						e.target.type === 'checkbox'
							? e.target.checked
								? 1
								: 0
							: e.target.value,
				},
				e.target,
			)
		})
	})
}

// Add listeners to action buttons.
const bindActions = () => {
	const controls = document.querySelectorAll('[data-em-action]')
	if (!controls[0]) return
	controls.forEach((ctrl) => {
		ctrl.addEventListener('click', () => {
			const action = ctrl.dataset.emAction
			if (action === 'test') {
				sendAction('test', { type: ctrl.dataset.emTest })
				return
			}
			if (action === 'fetch_logs') {
				sendAction('fetch_logs', { view: ctrl.dataset.emView }).then(
					(result) => {
						if (!result?.data?.html) return

						const container = document.querySelector(
							'[data-em-log-output]',
						)
						if (!container) return

						container.innerHTML = result.data.html
					},
				)
				return
			}
			if (action === 'apply_debug') {
				const payload = {}
				payload[ctrl.dataset.emDebug] = ctrl.checked
				sendAction(action, payload)
				return
			}
			sendAction(action)
		})
	})
}

/**
 * Prevent all form submissions (fetch replaces them).
 */
const disableFormSubmit = () => {
	document.querySelectorAll('.wpbody form').forEach((form) => {
		form.addEventListener('submit', (e) => e.preventDefault())
	})
}

/**
 * Remove validation state when user edits.
 */
const clearValidationOnInput = () => {
	document.addEventListener('input', (e) => {
		const field = e.target
		field.classList.remove('em-invalid')
		field.removeAttribute('aria-invalid')
	})
}

/**
 * Initialise.
 */
const init = () => {
	document.addEventListener(
		'DOMContentLoaded',
		() => {
			enableTestButtons()
			bindSettings()
			bindActions()
			disableFormSubmit()
			clearValidationOnInput()
		},
		{ once: true },
	)
}

export { init }
