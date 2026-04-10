import PropTypes from 'prop-types'
import styles from './Panel.module.scss'

/**
 * Panel.
 *
 * For housing main content.
 */
const Panel = ( {
	layout,
	children
} ) => {

	const className = [
		styles.panel,
		( layout === 'columns' ) && styles.gridColumns
	].filter( Boolean ).join( ' ' )

	return (
		<div className={className}>
			{children}
		</div>
	)
}

Panel.propTypes = {
	layout: PropTypes.string,
	children: PropTypes.node.isRequired
}

export { Panel }
