import { createRoot, createElement, Fragment, useEffect, useState } from '@wordpress/element'
import { useAdminActions, useAdminInlinedVars } from '../hooks'
import { Page, Header, Footer, Tabs } from './components/layout'
import { MonitorTab, LogsTab, EmailTab, LogFileTab } from './tabs'
import { useToast } from '../hooks'
import { Toast } from './components/feedback'
import { useWpAdminBarOffset } from '../hooks'

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
	const [ logView, setLogView ] = useState( 'grouped' )
	const pluginName = shellData.pluginData?.Name || 'Error Monitor'
	const pluginDescription = shellData.pluginData?.Description
	const PluginURI = shellData.pluginData?.PluginURI
	const PluginVersion = shellData.pluginData?.Version
	const AuthorName = shellData.pluginData?.Author
	const AuthorURI = shellData.pluginData?.AuthorURI
	const statusInfo = shellData.status || {}
	const tabs = Array.isArray( shellData.tabs ) ? shellData.tabs : []

	const handleFetchLogs = ( view ) => {
		setLogView( view )
		fetchLogs( view )
	}

	useWpAdminBarOffset()

	useEffect( () => {
		loadBootstrap()
	}, [] )

	useEffect( () => {
		if ( activeTab === 'logs' ) {
			handleFetchLogs( 'grouped' )
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
		loadingAction
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
			loadingAction
		} )
	}
	if ( activeTab === 'logs' ) {
		tabContent = createElement( LogsTab, {
			logsHTML,
			handleFetchLogs,
			loadingAction,
			logView
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
			loadingAction
		} )
	}

	return (
		<>
			<Page>
				<Header
					pluginName={pluginName}
					pluginDescription={pluginDescription}
					status={statusInfo}
				/>

				<Tabs
					activeTab={activeTab}
					tabs={tabs}
					onSelectTab={handleTabSelect}
				/>

				{tabContent}

				<Footer
					pluginName={pluginName}
					PluginURI={PluginURI}
					PluginVersion={PluginVersion}
					AuthorName={AuthorName}
					AuthorURI={AuthorURI}
				/>
			</Page>
			<Toast toasts={toasts} />
		</>
	)
}

const mountApp = () => {
	const rootNode = document.getElementById( 'errorMonitor' )

	if ( !rootNode || !useAdminInlinedVars ) {
		return
	}

	const root = createRoot( rootNode )

	root.render( createElement( Fragment, null, createElement( App ) ) )
}

export { mountApp }
