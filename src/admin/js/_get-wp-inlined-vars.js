/**
 * Grab vars added by wp_add_inline_script().
 *
 * Destructuring for verbose import.
 */

const getWpInlinedVars = () => {
	if ( typeof errorMonitorInlinedScript === 'undefined' ) return false

	return { ...errorMonitorInlinedScript }
}
const errorMonitorInlinedVars = getWpInlinedVars()

export { errorMonitorInlinedVars }
