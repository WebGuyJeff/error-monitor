const buildAPIURL = ( baseURL, path, query = {} ) => {
	const cleanBase = baseURL.replace( /\/$/, '' )
	const cleanPath = String( path || '' ).replace( /^\//, '' )

	const url = new URL( `${cleanBase}/${cleanPath}`, window.location.origin )

	Object.entries( query ).forEach( ( [ key, value ] ) => {
		if ( typeof value !== 'undefined' && value !== null ) {
			url.searchParams.set( key, value )
		}
	} )

	return url.toString()
}

export { buildAPIURL }
