import PropTypes from 'prop-types'
import styles from './Page.module.scss'

/**
 * Page layout.
 *
 * The top-level container for the plugin page.
 */
const Page = ( {
	children
} ) => {

	return (
		<div className={styles.page}>
			{children}
		</div>
	)
}

Page.propTypes = {
	children: PropTypes.node.isRequired
}

export { Page }
