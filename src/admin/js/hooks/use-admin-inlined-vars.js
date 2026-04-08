/**
 * Grab vars added by wp_add_inline_script().
 *
 * Destructuring for verbose import.
 */

const getAdminInlinedVars = () => {
	if ( typeof errorMonitorInlinedScript === 'undefined' ) return false

	return { ...errorMonitorInlinedScript }
}
const useAdminInlinedVars = getAdminInlinedVars()

export { useAdminInlinedVars }
