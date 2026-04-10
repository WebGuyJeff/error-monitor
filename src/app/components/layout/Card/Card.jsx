import PropTypes from 'prop-types'
import styles from './Card.module.scss'

/**
 * Card.
 *
 * Display content in cards within a panel.
 */
const Card = ( {
	children
} ) => {

	return (
		<div className={styles.card}>
			{children}
		</div>
	)
}

Card.propTypes = {
	children: PropTypes.node.isRequired
}

export { Card }
