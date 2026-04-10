import { fetchHttpRequest } from '../utils/fetch-http'
import { buildAPIURL } from '../utils/build-api-url'
import { useAdminInlinedVars } from './use-admin-inlined-vars'

const useAPI = ( { showToast } = {} ) => {
	const { restBaseURL = '', restNonce } = useAdminInlinedVars || {}

	const sendRequest = async (
		path,
		{ method = 'GET', body, showFeedback = true, query = {} } = {}
	) => {
		const result = await fetchHttpRequest(
			buildAPIURL( restBaseURL, path, query ),
			{
				method,
				headers: {
					'X-WP-Nonce': restNonce,
					'Content-Type': 'application/json',
				},
				body: body ? JSON.stringify( body ) : undefined,
			}
		)

		if ( showFeedback && result?.output?.[ 0 ] && showToast ) {
			showToast( result.output[ 0 ], result.ok ? 'success' : 'danger' )
		}

		return result
	}

	return { sendRequest }
}

export { useAPI }
