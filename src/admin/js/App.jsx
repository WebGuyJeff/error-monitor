import { createRoot, createElement, Fragment, useEffect, useState } from '@wordpress/element'
import { useAdminActions, useAdminInlinedVars } from './hooks'
import { Header, Footer, TabNav } from './components/layout'
import { MonitorTab, LogsTab, EmailTab, LogFileTab } from './tabs'
import { useToast } from './hooks/use-toast'
import { Toast } from './components/feedback'

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
	const { toasts, showToast } = useToast()
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
	} = useAdminActions( { showToast } )
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

	return (
		<>
			<Header pluginName={pluginName} status={statusInfo} />

			<div className="adminPage_body">
				<TabNav
					activeTab={activeTab}
					tabs={tabs}
					onSelectTab={handleTabSelect}
				/>

				<div className="tab_content">
					{tabContent}
				</div>
			</div>

			<Footer pluginName={pluginName} />

			<Toast toasts={toasts} />
		</>
	)
}

const mountApp = () => {
	const rootNode = document.getElementById( 'errorMonitorReactRoot' )

	if ( !rootNode || !useAdminInlinedVars ) {
		return
	}

	const root = createRoot( rootNode )

	root.render( createElement( Fragment, null, createElement( App ) ) )
}

export { mountApp }
