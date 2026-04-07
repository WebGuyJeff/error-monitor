import { createRoot, createElement, Fragment, useEffect, useState } from '@wordpress/element'
import { errorMonitorInlinedVars } from './_get-wp-inlined-vars'
import { useAdminActions } from './use-admin-actions'
import { Header } from './components/header'
import { TabNav } from './components/tab-nav'
import { MonitorTab } from './tabs/monitor-tab'
import { EmailTab } from './tabs/email-tab'
import { LogFileTab } from './tabs/log-file-tab'
import { LogsTab } from './tabs/logs-tab'

const getActiveTabFromURL = () => {
	const query = new URLSearchParams( window.location.search )

	return query.get( 'tab' ) || 'monitor'
}

const updateHistoryTab = ( tab ) => {
	const url = new URL( window.location.href )

	if ( tab === 'monitor' ) {
		url.searchParams.delete( 'tab' )
	} else {
		url.searchParams.set( 'tab', tab )
	}
	window.history.pushState( {}, '', url.toString() )
}

const App = () => {
	const {
		settingsState,
		status,
		shellData,
		invalidField,
		loadingAction,
		smtpOutput,
		logsHTML,
		loadBootstrap,
		updateSetting,
		debouncedUpdateSetting,
		flushUpdateSetting,
		refreshLogStatus,
		discoverLog,
		runManualScan,
		runTest,
		toggleDebug,
		fetchLogs,
	} = useAdminActions()
	const [ activeTab, setActiveTab ] = useState( getActiveTabFromURL() )
	const pluginName = shellData.pluginName || 'Error Monitor'
	const statusInfo = shellData.status || {}
	const tabs = Array.isArray( shellData.tabs ) ? shellData.tabs : []

	useEffect( () => {
		loadBootstrap()
	}, [] )

	useEffect( () => {
		if ( activeTab === 'logs' ) {
			fetchLogs( 'grouped' )
		}
		if ( activeTab === 'log-file' ) {
			refreshLogStatus()
		}
	}, [ activeTab ] )

	useEffect( () => {
		const onPopState = () => setActiveTab( getActiveTabFromURL() )

		window.addEventListener( 'popstate', onPopState )

		return () => window.removeEventListener( 'popstate', onPopState )
	}, [] )

	const handleTabSelect = ( tab ) => {
		if ( !tab || tab === activeTab ) {
			return
		}
		setActiveTab( tab )
		updateHistoryTab( tab )
	}

	let tabContent = createElement( MonitorTab, {
		settingsState,
		updateSetting,
		debouncedUpdateSetting,
		flushUpdateSetting,
		runManualScan,
		invalidField,
		loadingAction,
	} )

	if ( activeTab === 'email' ) {
		tabContent = createElement( EmailTab, {
			settingsState,
			updateSetting,
			debouncedUpdateSetting,
			flushUpdateSetting,
			runTest,
			smtpOutput,
			invalidField,
			loadingAction,
		} )
	}
	if ( activeTab === 'logs' ) {
		tabContent = createElement( LogsTab, {
			logsHTML,
			fetchLogs,
			loadingAction,
		} )
	}
	if ( activeTab === 'log-file' ) {
		tabContent = createElement( LogFileTab, {
			settingsState,
			updateSetting,
			debouncedUpdateSetting,
			flushUpdateSetting,
			invalidField,
			discoverLog,
			status,
			toggleDebug,
			loadingAction,
		} )
	}

	return createElement(
		Fragment,
		null,
		createElement( Header, { pluginName, status: statusInfo } ),
		createElement(
			'div',
			{ className: 'adminPage_body' },
			createElement( TabNav, { activeTab, tabs, onSelectTab: handleTabSelect } ),
			createElement( 'div', { className: 'tab_content' }, tabContent ),
		),
	)
}

const mountApp = () => {
	const rootNode = document.getElementById( 'errorMonitorReactRoot' )

	if ( !rootNode || !errorMonitorInlinedVars ) {
		return
	}

	const root = createRoot( rootNode )

	root.render( createElement( Fragment, null, createElement( App ) ) )
}

export { mountApp }
