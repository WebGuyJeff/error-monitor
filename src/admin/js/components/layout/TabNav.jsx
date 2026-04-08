import PropTypes from 'prop-types'

const TabNav = ( { activeTab, tabs, onSelectTab } ) => (
	<div className="adminPage_container">
		<nav className="adminPage_nav">
			{tabs.map( ( tab ) => {
				const isActive =
					tab.slug === activeTab ||
					( activeTab === 'monitor' && tab.slug === 'monitor' )

				return (
					<a
						key={tab.slug}
						href={`#tab-${tab.slug}`}
						onClick={( event ) => {
							event.preventDefault()
							onSelectTab( tab.slug )
						}}
						className={`nav-tab${isActive ? ' nav-tab-active' : ''}`}
					>
						{tab.title}
					</a>
				)
			} )}
		</nav>
	</div>
)

TabNav.propTypes = {
	activeTab: PropTypes.string.isRequired,

	tabs: PropTypes.arrayOf(
		PropTypes.shape( {
			slug: PropTypes.string.isRequired,
			title: PropTypes.string.isRequired,
		} )
	).isRequired,

	onSelectTab: PropTypes.func.isRequired,
}

export { TabNav }
