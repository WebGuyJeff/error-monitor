import PropTypes from 'prop-types'
import styles from './Button.module.scss'

/**
 * Button.
 */
const Button = ( {
	label,
	type = 'button',
	variant = 'secondary',
	disabled,
	onClick
} ) => {

	const variations = {
		primary: styles.primary,
		secondary: styles.secondary,
	}

	const className = [
		styles.button,
		variations[ variant ],
	].filter( Boolean ).join( ' ' )

	return (
		<button
			type={type}
			className={className}
			disabled={disabled}
			onClick={onClick}
		>
			{ label }
		</button>
	)
}

Button.propTypes = {
	label: PropTypes.string.isRequired,
	type: PropTypes.string,
	variant: PropTypes.string,
	disabled: PropTypes.bool,
	onClick: PropTypes.func.isRequired,
}

export { Button }
