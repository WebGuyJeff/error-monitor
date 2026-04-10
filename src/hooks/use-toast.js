import { useState, useCallback } from '@wordpress/element'

const useToast = () => {
	const [ toasts, setToasts ] = useState( [] )

	const showToast = useCallback( ( message, type = 'success' ) => {
		const id = Date.now() + Math.random()

		setToasts( ( current ) => [
			...current,
			{ id, message, type },
		] )

		// Auto remove
		setTimeout( () => {
			setToasts( ( current ) =>
				current.filter( ( toast ) => toast.id !== id )
			)
		}, 9999999 ) // 4000
	}, [] )

	const removeToast = useCallback( ( id ) => {
		setToasts( ( current ) =>
			current.filter( ( toast ) => toast.id !== id )
		)
	}, [] )

	return {
		toasts,
		showToast,
		removeToast,
	}
}

export { useToast }
