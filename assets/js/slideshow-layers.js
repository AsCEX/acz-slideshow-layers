( function () {
	'use strict';

	const instances = new WeakMap();

	function parseOptions( element ) {
		try {
			return JSON.parse( element.getAttribute( 'data-options' ) || '{}' );
		} catch ( error ) {
			return {};
		}
	}

	function initSlideshow( element ) {
		if ( instances.has( element ) ) {
			instances.get( element ).destroy();
		}

		const slides = Array.from( element.querySelectorAll( '.acz-slideshow-layers__slide' ) );
		const dots = Array.from( element.querySelectorAll( '.acz-slideshow-layers__dot' ) );
		const prev = element.querySelector( '.acz-slideshow-layers__arrow--prev' );
		const next = element.querySelector( '.acz-slideshow-layers__arrow--next' );
		const options = Object.assign(
			{
				autoplay: false,
				autoplaySpeed: 5000,
				transitionSpeed: 350,
				loop: true,
				pauseOnHover: true,
			},
			parseOptions( element )
		);

		if ( ! slides.length ) {
			return;
		}

		let activeIndex = Math.max(
			0,
			slides.findIndex( ( slide ) => slide.classList.contains( 'is-active' ) )
		);
		let timer = null;
		let pointerStartX = 0;
		let pointerStartY = 0;
		let pointerDeltaX = 0;
		let pointerId = null;
		let isDragging = false;
		let isSwiping = false;

		element.style.setProperty( '--acz-slide-transition', `${ options.transitionSpeed }ms` );

		function setActive( index ) {
			const lastIndex = slides.length - 1;

			if ( index < 0 ) {
				index = options.loop ? lastIndex : 0;
			}

			if ( index > lastIndex ) {
				index = options.loop ? 0 : lastIndex;
			}

			activeIndex = index;

			slides.forEach( ( slide, slideIndex ) => {
				const isActive = slideIndex === activeIndex;
				slide.classList.toggle( 'is-active', isActive );
				slide.classList.toggle( 'is-before', slideIndex < activeIndex );
				slide.classList.toggle( 'is-after', slideIndex > activeIndex );
				slide.setAttribute( 'aria-hidden', isActive ? 'false' : 'true' );
			} );

			dots.forEach( ( dot, dotIndex ) => {
				dot.classList.toggle( 'is-active', dotIndex === activeIndex );
			} );
		}

		function stopAutoplay() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function startAutoplay() {
			stopAutoplay();

			if ( options.autoplay && slides.length > 1 ) {
				timer = window.setInterval( () => setActive( activeIndex + 1 ), options.autoplaySpeed );
			}
		}

		function isInteractiveTarget( target ) {
			return !! target.closest( 'button, a, input, select, textarea, [role="button"], [data-no-swipe]' );
		}

		function getSwipeThreshold() {
			return Math.min( 120, Math.max( 40, element.offsetWidth * 0.12 ) );
		}

		function setDraggingOffset( deltaX ) {
			const width = Math.max( 1, element.offsetWidth );
			const percent = ( deltaX / width ) * 100;

			element.classList.add( 'is-dragging' );

			slides.forEach( ( slide, slideIndex ) => {
				const offset = slideIndex - activeIndex;
				slide.style.transform = `translateX(${ ( offset * 100 ) + percent }%)`;
			} );
		}

		function clearDraggingOffset() {
			element.classList.remove( 'is-dragging' );

			slides.forEach( ( slide ) => {
				slide.style.transform = '';
			} );
		}

		function onPointerDown( event ) {
			if ( slides.length < 2 || event.button > 0 || isInteractiveTarget( event.target ) ) {
				return;
			}

			pointerStartX = event.clientX;
			pointerStartY = event.clientY;
			pointerDeltaX = 0;
			pointerId = event.pointerId;
			isDragging = true;
			isSwiping = false;
			stopAutoplay();

			if ( event.currentTarget.setPointerCapture ) {
				event.currentTarget.setPointerCapture( pointerId );
			}
		}

		function onPointerMove( event ) {
			if ( ! isDragging || pointerId !== event.pointerId ) {
				return;
			}

			const deltaX = event.clientX - pointerStartX;
			const deltaY = event.clientY - pointerStartY;

			if ( ! isSwiping && Math.abs( deltaY ) > Math.abs( deltaX ) && Math.abs( deltaY ) > 12 ) {
				isDragging = false;
				startAutoplay();
				return;
			}

			if ( Math.abs( deltaX ) > 8 && Math.abs( deltaX ) > Math.abs( deltaY ) ) {
				isSwiping = true;
			}

			if ( ! isSwiping ) {
				return;
			}

			event.preventDefault();
			pointerDeltaX = deltaX;
			setDraggingOffset( deltaX );
		}

		function onPointerUp( event ) {
			if ( ! isDragging || pointerId !== event.pointerId ) {
				return;
			}

			const shouldMove = isSwiping && Math.abs( pointerDeltaX ) >= getSwipeThreshold();

			clearDraggingOffset();

			if ( shouldMove ) {
				setActive( pointerDeltaX < 0 ? activeIndex + 1 : activeIndex - 1 );
			}

			isDragging = false;
			isSwiping = false;
			pointerId = null;
			pointerDeltaX = 0;
			startAutoplay();
		}

		function onPointerCancel( event ) {
			if ( pointerId !== event.pointerId ) {
				return;
			}

			clearDraggingOffset();
			isDragging = false;
			isSwiping = false;
			pointerId = null;
			pointerDeltaX = 0;
			startAutoplay();
		}

		function onDotClick( event ) {
			const index = dots.indexOf( event.currentTarget );

			if ( -1 === index ) {
				return;
			}

			setActive( index );
			startAutoplay();
		}

		function onPrevClick() {
			setActive( activeIndex - 1 );
			startAutoplay();
		}

		function onNextClick() {
			setActive( activeIndex + 1 );
			startAutoplay();
		}

		function stopControlPointerPropagation( event ) {
			event.stopPropagation();
		}

		dots.forEach( ( dot ) => {
			dot.addEventListener( 'click', onDotClick );
			dot.addEventListener( 'pointerdown', stopControlPointerPropagation );
		} );

		if ( prev ) {
			prev.addEventListener( 'click', onPrevClick );
			prev.addEventListener( 'pointerdown', stopControlPointerPropagation );
		}

		if ( next ) {
			next.addEventListener( 'click', onNextClick );
			next.addEventListener( 'pointerdown', stopControlPointerPropagation );
		}

		element.addEventListener( 'pointerdown', onPointerDown );
		element.addEventListener( 'pointermove', onPointerMove );
		element.addEventListener( 'pointerup', onPointerUp );
		element.addEventListener( 'pointercancel', onPointerCancel );
		element.addEventListener( 'lostpointercapture', onPointerCancel );

		if ( options.pauseOnHover ) {
			element.addEventListener( 'mouseenter', stopAutoplay );
			element.addEventListener( 'mouseleave', startAutoplay );
		}

		setActive( activeIndex );
		startAutoplay();

		instances.set( element, {
			destroy() {
				stopAutoplay();
				dots.forEach( ( dot ) => {
					dot.removeEventListener( 'click', onDotClick );
					dot.removeEventListener( 'pointerdown', stopControlPointerPropagation );
				} );

				if ( prev ) {
					prev.removeEventListener( 'click', onPrevClick );
					prev.removeEventListener( 'pointerdown', stopControlPointerPropagation );
				}

				if ( next ) {
					next.removeEventListener( 'click', onNextClick );
					next.removeEventListener( 'pointerdown', stopControlPointerPropagation );
				}

				element.removeEventListener( 'pointerdown', onPointerDown );
				element.removeEventListener( 'pointermove', onPointerMove );
				element.removeEventListener( 'pointerup', onPointerUp );
				element.removeEventListener( 'pointercancel', onPointerCancel );
				element.removeEventListener( 'lostpointercapture', onPointerCancel );
			},
		} );
	}

	function initAll( root ) {
		if ( root.matches && root.matches( '.acz-slideshow-layers' ) ) {
			initSlideshow( root );
		}

		root.querySelectorAll( '.acz-slideshow-layers' ).forEach( initSlideshow );
	}

	window.ACZSlideshowLayers = window.ACZSlideshowLayers || {};
	window.ACZSlideshowLayers.initAll = initAll;

	function attachElementorHook() {
		if ( ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return;
		}

		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/acz-slideshow-layers.default', ( scope ) => {
			initAll( scope[ 0 ] || document );
		} );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', attachElementorHook );
	}

	attachElementorHook();

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', () => initAll( document ) );
	} else {
		initAll( document );
	}

	document.addEventListener( 'acz-slideshow-layers:refresh', ( event ) => {
		initAll( event.target || document );
	} );
}() );
