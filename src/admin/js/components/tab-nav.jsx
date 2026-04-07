import { createElement } from '@wordpress/element'

const TabNav = ( { activeTab, tabs, onSelectTab } ) =>
	createElement(
		'div',
		{ className: 'adminPage_container' },
		createElement(
			'nav',
			{ className: 'adminPage_nav' },
			tabs.map( ( tab ) => {
				const isActive = tab.slug === activeTab || ( activeTab === 'monitor' && tab.slug === 'monitor' )

				return createElement(
					'a',
					{
						key: tab.slug,
						href: `#tab-${tab.slug}`,
						onClick: ( event ) => {
							event.preventDefault()
							onSelectTab( tab.slug )
						},
						className: `nav-tab${isActive ? ' nav-tab-active' : ''}`,
					},
					tab.title,
				)
			} ),
		),
	)

export { TabNav }
