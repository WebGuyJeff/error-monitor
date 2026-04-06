/**
 * Grab vars added by wp_add_inline_script().
 *
 * Destructuring for verbose import.
 */

const getWpInlinedVars = () => {
	if (typeof errorMonitorInlinedScript === 'undefined') return false
	const {
		settingsOK, // Boolean value indicating email settings are configured.
		restActionURL, // REST API store endpoint.
		restNonce, // WP nonce string.
	} = errorMonitorInlinedScript
	return { ...errorMonitorInlinedScript }
}
const errorMonitorInlinedVars = getWpInlinedVars()

export { errorMonitorInlinedVars }
