import PropTypes from 'prop-types'
import styles from './Toast.module.scss'

const Toast = ( { toasts } ) => (
	<div
		className={styles.toastContainer}
		aria-live="polite"
	>
		{toasts.map( ( toast ) => {

			const className = [
				styles.toast,
				( toast.type === 'danger' ) && styles.danger,
				( toast.type === 'success' ) && styles.success,
			].filter( Boolean ).join( ' ' )

			return (
				<div
					key={toast.id}
					className={className}
					role="status"
				>
					{toast.message}
				</div>
			)
		} )}
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
