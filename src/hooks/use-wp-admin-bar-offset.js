import { useEffect } from 'react'

const useWpAdminBarOffset = ( varName = '--em_wpadminbarHeight_jsControlled' ) => {

	const root = document.getElementById( 'errorMonitor' )

	if ( ! root ) {
		console.error( 'Element with ID "errorMonitor" not found.' )

		return
	}

	useEffect( () => {
		const update = () => {

			const el = document.getElementById( 'wpadminbar' )

			if ( !el ) {
				root.style.setProperty( varName, '0px' )

				return
			}

			const rect = el.getBoundingClientRect()
			let offset = rect.bottom

			if ( offset < 0 ) offset = 0

			root.style.setProperty( varName, `${offset}px` )
		}

		// Initial run.
		update()

		// RAF throttled scroll handler.
		let ticking = false
		const onScroll = () => {
			if ( !ticking ) {
				requestAnimationFrame( () => {
					update()
					ticking = false
				} )
				ticking = true
			}
		}

		window.addEventListener( 'resize', update )
		window.addEventListener( 'scroll', onScroll, { passive: true } )

		const observer = new MutationObserver( update )
		const el = document.getElementById( 'wpadminbar' )

		if ( el ) {
			observer.observe( el, { childList: true, subtree: true } )
		}

		return () => {
			window.removeEventListener( 'resize', update )
			window.removeEventListener( 'scroll', onScroll )
			observer.disconnect()
		}
	}, [ varName ] )
}

export { useWpAdminBarOffset }
