/**
 * Perform a Fetch request with timeout and json response.
 * 
 * @param {string} url      The WP plugin REST endpoint url.
 * @param {object} options  An object of fetch API options.
 * @return {object}         An object of message strings and ok flag.
 * 
 */
async function fetchHttpRequest( url, options ) {

	try {
		const response = await fetch( url, { ...options, signal: AbortSignal.timeout( 14000 ) } )
		const result = await response.json()
		result.ok = response.ok
		if ( ! result.ok ) throw result
		return result

	} catch ( error ) {
		if ( error.name === 'TimeoutError' ) {
			// Request timed out.
			error.output = [ 'The request timed out, please try again. If the issue persists, please report to the plugin maintainer.' ]
			error.ok = false
			console.error( error )
		} else if ( ! error.output ) {
			// Likely no server response, so display a generic error to the user.
			error.output = [ 'There was a problem communicating with the server, please try again. If the issue persists, please report to the plugin maintainer.' ]
			error.ok = false
			console.error( error )
		}
		for ( const message in error.output ) {
			console.error( error.output[ message ] )
		}
		return error

	}
}

export { fetchHttpRequest }
