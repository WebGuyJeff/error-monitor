import PropTypes from 'prop-types'

const Toast = ( { toasts } ) => (
	<div
		id="errorMonitor__toastContainer"
		aria-live="polite"
		style={{
			position: 'fixed',
			top: '20px',
			right: '20px',
		}}
	>
		{toasts.map( ( toast ) => (
			<div
				key={toast.id}
				className={`em-toast em-toast-${toast.type}`}
				role="status"
			>
				{toast.message}
			</div>
		) )}
	</div>
)

Toast.propTypes = {
	toasts: PropTypes.arrayOf(
		PropTypes.shape( {
			id: PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] )
				.isRequired,
			message: PropTypes.string.isRequired,
			type: PropTypes.string,
		} )
	).isRequired,
}

export { Toast }
