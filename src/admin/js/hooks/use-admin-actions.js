import { useRef, useState } from '@wordpress/element'
import { useAPI } from './use-api'

const useAdminActions = ( { showToast } ) => {
	const [ settingsState, setSettingsState ] = useState( {} )
	const [ status, setStatus ] = useState( {} )
	const [ shellData, setShellData ] = useState( {
		pluginName: 'Error Monitor',
		status: {},
		tabs: [],
	} )
	const [ invalidField, setInvalidField ] = useState( '' )
	const [ loadingAction, setLoadingAction ] = useState( '' )
	const [ smtpOutput, setSmtpOutput ] = useState( [] )
	const [ logsHTML, setLogsHTML ] = useState( '' )
	const requestKeys = useRef( {
		bootstrap: 0,
		logStatus: 0,
		logs: 0,
	} )
	const debounceTimers = useRef( {} )
	const settingRequestKeys = useRef( {} )
	const { sendRequest } = useAPI( { showToast } )

	const loadBootstrap = async () => {
		const requestKey = requestKeys.current.bootstrap + 1

		requestKeys.current.bootstrap = requestKey
		const result = await sendRequest( 'bootstrap', {
			method: 'GET',
			showFeedback: false
		} )

		if ( requestKeys.current.bootstrap !== requestKey ) {
			return result
		}
		if ( result.ok && result.data ) {
			setSettingsState( result.data.settings || {} )
			setStatus( result.data.logFileStatus || {} )
			setShellData( {
				pluginName: result.data.pluginName || 'Error Monitor',
				status: result.data.status || {},
				tabs: Array.isArray( result.data.tabs ) ? result.data.tabs : [],
			} )
		}

		return result
	}

	const updateSetting = async ( key, value ) => {
		setSettingsState( ( current ) => ( { ...current, [ key ]: value } ) )

		const requestKey = ( settingRequestKeys.current[ key ] || 0 ) + 1

		settingRequestKeys.current[ key ] = requestKey

		const result = await sendRequest( 'settings', {
			method: 'POST',
			body: { key, value },
		} )

		if ( settingRequestKeys.current[ key ] !== requestKey ) {
			return
		}

		if ( result.ok ) {
			setInvalidField( '' )
		} else if ( result?.data?.field ) {
			setInvalidField( result.data.field )
		}
	}

	const debouncedUpdateSetting = ( key, value, delay = 400 ) => {
		setSettingsState( ( current ) => ( { ...current, [ key ]: value } ) )

		if ( debounceTimers.current[ key ] ) {
			clearTimeout( debounceTimers.current[ key ] )
		}

		debounceTimers.current[ key ] = setTimeout( () => {
			updateSetting( key, value )
		}, delay )
	}

	const flushUpdateSetting = ( key, value ) => {
		if ( debounceTimers.current[ key ] ) {
			clearTimeout( debounceTimers.current[ key ] )
		}

		updateSetting( key, value )
	}

	const refreshLogStatus = async () => {
		const requestKey = requestKeys.current.logStatus + 1

		requestKeys.current.logStatus = requestKey
		const result = await sendRequest( 'status/log-file', { method: 'GET', showFeedback: false } )

		if ( requestKeys.current.logStatus !== requestKey ) {
			return null
		}
		if ( result.ok && result.data ) {
			setStatus( result.data )

			return result.data
		}

		return null
	}

	const discoverLog = async () => {
		setLoadingAction( 'discover_log' )
		const result = await sendRequest( 'status/log-file/discover', { method: 'POST' } )

		if ( result.ok ) {
			const refreshed = await refreshLogStatus()

			if ( refreshed?.path ) {
				setSettingsState( ( current ) => ( { ...current, log_file_path: refreshed.path } ) )
			}
		}
		setLoadingAction( '' )
	}

	const runManualScan = async () => {
		setLoadingAction( 'manual_scan' )
		await sendRequest( 'monitor/scan', { method: 'POST' } )
		setLoadingAction( '' )
	}

	const runTest = async ( type ) => {
		setLoadingAction( `test_${type}` )
		const result = await sendRequest( 'email/test', {
			method: 'POST',
			body: { type },
		} )
		const data = Array.isArray( result?.data ) ? result.data : [ 'No output returned.' ]

		if ( type === 'smtp' ) {
			setSmtpOutput( data.map( ( message ) => ( { message, status: result.ok ? 'success' : 'danger' } ) ) )
		}
		setLoadingAction( '' )
	}

	const toggleDebug = async ( key, value ) => {
		setLoadingAction( 'apply_debug' )
		await sendRequest( 'status/debug', {
			method: 'POST',
			body: { [ key ]: value },
		} )
		await refreshLogStatus()
		setLoadingAction( '' )
	}

	const fetchLogs = async ( view = 'grouped' ) => {
		setLoadingAction( `fetch_${view}` )
		const requestKey = requestKeys.current.logs + 1

		requestKeys.current.logs = requestKey
		const result = await sendRequest( 'logs', {
			method: 'GET',
			query: { view },
			showFeedback: false,
		} )

		if ( requestKeys.current.logs !== requestKey ) {
			setLoadingAction( '' )

			return
		}
		if ( result.ok && result.data?.html ) {
			setLogsHTML( result.data.html )
		}
		setLoadingAction( '' )
	}

	return {
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
	}
}

export { useAdminActions }
