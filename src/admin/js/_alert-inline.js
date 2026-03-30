const logWrapperSelector = '#errorMonitor__smtpLogs'
const logoutputSelector = '#errorMonitor__smtpLogOutput'

/**
 * Remove all child nodes from the output node.
 *
 * @param {object} form The form containing the output node to remove children from.
 *
 */
function cleanOutput(form) {
	const output = form.querySelector(logoutputSelector)
	output.replaceChildren()
}

/**
 * Add alerts to the output.
 *
 * @param {object} form The target form.
 * @param {array} alerts Alert objects to be displayed.
 */
const addAlerts = (form, alerts) => {
	const container = form.querySelector(logWrapperSelector)
	const output = form.querySelector(logoutputSelector)
	if (!container || !output) return

	container.style.display = 'block'
	insertIntoDom(output, alerts)
}

/**
 * Create an array of popout message elements and insert into dom.
 *
 * @param {object} parentElement The parent node to append to.
 * @param {array}  alerts An array of alerts as objects.
 *
 */
function insertIntoDom(output, alerts) {
	if (!output || output.nodeType !== Node.ELEMENT_NODE) {
		console.error(`'output' must be an element node.`)
		return
	} else if (!isIterable(alerts)) {
		console.error(
			`'alerts' must be non-string iterable. ${typeof alerts} found.`,
		)
		return
	}
	const classBlock = 'errorMonitor__alert',
		classModifier = {
			danger: '-danger',
			success: '-success',
			info: '-info',
			warning: '-warning',
		}
	alerts.forEach((alert) => {
		let span = document.createElement('span')
		span.innerText = alert.text
		const classNames = [classBlock, classBlock + classModifier[alert.type]]
		classNames.forEach((className) => span.classList.add(className))
		output.appendChild(span)
	})
}

/**
 * Check if passed variable is iterable.
 *
 */
function isIterable(object) {
	// Check for null and undefined.
	if (object === null || object === undefined) {
		return false
	}
	return typeof object[Symbol.iterator] === 'function'
}

export { addAlerts, cleanOutput }
