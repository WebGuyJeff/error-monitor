import PropTypes from 'prop-types'
import styles from './Tabs.module.scss'

const Tabs = ( { activeTab, tabs, onSelectTab } ) => (
	<nav className={styles.tabs}>
		{tabs.map( ( tab ) => {
			const isActive =
				tab.slug === activeTab ||
				( activeTab === 'monitor' && tab.slug === 'monitor' )

			const className = [
				styles.tab,
				isActive && styles.tab__active,
			].filter( Boolean ).join( ' ' )

			return (
				<a
					key={tab.slug}
					href={`#tab-${tab.slug}`}
					onClick={( event ) => {
						event.preventDefault()
						onSelectTab( tab.slug )
					}}
					className={className}
				>
					{tab.title}
				</a>
			)
		} )}
	</nav>
)

Tabs.propTypes = {
	activeTab: PropTypes.string.isRequired,

	tabs: PropTypes.arrayOf(
		PropTypes.shape( {
			slug: PropTypes.string.isRequired,
			title: PropTypes.string.isRequired,
		} )
	).isRequired,

	onSelectTab: PropTypes.func.isRequired,
}

export { Tabs }
