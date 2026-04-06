const showToast = (message, type = 'success') => {
	let container = document.querySelector('#errorMonitor__toastContainer')

	if (!container) {
		container = document.createElement('div')
		container.id = 'errorMonitor__toastContainer'
		container.setAttribute('aria-live', 'polite')
		container.style.position = 'fixed'
		container.style.top = '20px'
		container.style.right = '20px'
		document.body.appendChild(container)
	}

	const toast = document.createElement('div')
	toast.className = `em-toast em-toast-${type}`
	toast.setAttribute('role', 'status')
	toast.textContent = message

	container.appendChild(toast)

	setTimeout(() => {
		toast.style.opacity = '0'
		setTimeout(() => toast.remove(), 300)
	}, 4000)
}

export { showToast }
