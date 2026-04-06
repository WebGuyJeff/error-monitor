const logoutputSelector = '#errorMonitor__consoleOutput'

/**
 * Display SMTP test logs.
 *
 * @param {object} form The target form.
 * @param {array} alerts Alert objects to be displayed.
 */
const consoleOutput = (alerts, status = 'default') => {
	const output = document.querySelector(logoutputSelector)
	if (!output) return

	output.replaceChildren()
	output.style.display = 'block'

	const classBlock = 'errorMonitor__alert'
	const classModifier = {
		default: '',
		danger: '-danger',
		success: '-success',
	}

	if (!Array.isArray(alerts)) {
		alerts = [alerts]
	}

	alerts.forEach((alert) => {
		let p = document.createElement('p')
		p.innerText = alert
		const classNames = [classBlock, classBlock + classModifier[status]]
		classNames.forEach((className) => p.classList.add(className))
		output.appendChild(p)
	})
}

export { consoleOutput }
