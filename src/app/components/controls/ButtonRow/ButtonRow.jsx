import PropTypes from 'prop-types'
import styles from './ButtonRow.module.scss'

/**
 * Button row wrapper.
 */
const ButtonRow = ( {
	layout = 'default',
	children
} ) => {

	const className = [
		styles.buttonRow,
		( layout === 'conjoined' ) && styles.conjoined,
	].filter( Boolean ).join( ' ' )

	return (
		<div className={className}>
			{children}
		</div>
	)
}

ButtonRow.propTypes = {
	layout: PropTypes.string,
	children: PropTypes.node.isRequired
}

export { ButtonRow }
