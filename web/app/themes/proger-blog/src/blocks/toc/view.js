/* eslint-env browser */

const HEADER_OFFSET = 80;
const TOC_ROOT_SELECTOR = '.toc';
const TOC_LINK_SELECTOR = '.toc__link[data-target]';
const FLOATING_TOC_SELECTOR = '.single-floating-toc';

const prefersReduced = () =>
	typeof window.matchMedia === 'function' &&
	window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

const closeFloatingToc = ( details ) => {
	if ( details instanceof HTMLDetailsElement ) {
		details.removeAttribute( 'open' );
	}
};

const focusHeading = ( target ) => {
	const previousTabIndex = target.getAttribute( 'tabindex' );

	target.setAttribute( 'tabindex', '-1' );
	target.focus( { preventScroll: true } );

	if ( previousTabIndex === null ) {
		window.setTimeout( () => target.removeAttribute( 'tabindex' ), 300 );
	}
};

const scrollToHeading = ( targetSlug ) => {
	if ( ! targetSlug ) {
		return false;
	}

	const target = document.getElementById( targetSlug );

	if ( ! target ) {
		return false;
	}

	const top =
		target.getBoundingClientRect().top + window.scrollY - HEADER_OFFSET;

	window.scrollTo( {
		top,
		behavior: prefersReduced() ? 'auto' : 'smooth',
	} );

	history.replaceState( null, '', `#${ targetSlug }` );
	focusHeading( target );

	return true;
};

const bindFloatingToc = ( details ) => {
	if (
		! ( details instanceof HTMLDetailsElement ) ||
		details.dataset.tocBound === 'true'
	) {
		return;
	}

	details.dataset.tocBound = 'true';
	details.addEventListener( 'click', ( event ) => {
		if ( ! ( event.target instanceof Element ) ) {
			return;
		}

		const closeTarget = event.target.closest( '[data-toc-close]' );

		if ( ! closeTarget || ! details.contains( closeTarget ) ) {
			return;
		}

		event.preventDefault();
		closeFloatingToc( details );
	} );
};

const bindTocRoot = ( root ) => {
	if (
		! ( root instanceof HTMLElement ) ||
		root.dataset.tocReady === 'true'
	) {
		return;
	}

	root.dataset.tocReady = 'true';
	root.addEventListener( 'click', ( event ) => {
		if ( ! ( event.target instanceof Element ) ) {
			return;
		}

		const link = event.target.closest( TOC_LINK_SELECTOR );

		if ( ! link || ! root.contains( link ) ) {
			return;
		}

		const targetSlug = link.getAttribute( 'data-target' );

		if ( ! scrollToHeading( targetSlug ) ) {
			return;
		}

		event.preventDefault();
		closeFloatingToc( link.closest( FLOATING_TOC_SELECTOR ) );
	} );
};

const initScrollSpy = ( roots ) => {
	if ( ! roots.length || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const slugToLinks = new Map();

	roots.forEach( ( root ) => {
		root.querySelectorAll( TOC_LINK_SELECTOR ).forEach( ( link ) => {
			const slug = link.getAttribute( 'data-target' );

			if ( ! slug ) {
				return;
			}

			const links = slugToLinks.get( slug ) ?? [];
			links.push( link );
			slugToLinks.set( slug, links );
		} );
	} );

	const headings = Array.from( slugToLinks.keys() )
		.map( ( slug ) => document.getElementById( slug ) )
		.filter( Boolean );

	if ( ! headings.length ) {
		return;
	}

	let activeSlug = null;
	const visible = new Set();

	const setActive = ( slug ) => {
		if ( slug === activeSlug ) {
			return;
		}

		if ( activeSlug ) {
			( slugToLinks.get( activeSlug ) ?? [] ).forEach( ( link ) => {
				link.classList.remove( 'is-active' );
				link.removeAttribute( 'aria-current' );
			} );
		}

		if ( slug ) {
			( slugToLinks.get( slug ) ?? [] ).forEach( ( link ) => {
				link.classList.add( 'is-active' );
				link.setAttribute( 'aria-current', 'location' );
			} );
		}

		activeSlug = slug;
	};

	const resolveActiveSlug = () => {
		const firstVisible = headings.find( ( heading ) =>
			visible.has( heading.id )
		);

		if ( firstVisible ) {
			return firstVisible.id;
		}

		const passedHeading = headings
			.slice()
			.reverse()
			.find(
				( heading ) =>
					heading.getBoundingClientRect().top <= HEADER_OFFSET + 12
			);

		return passedHeading ? passedHeading.id : null;
	};

	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					visible.add( entry.target.id );
					return;
				}

				visible.delete( entry.target.id );
			} );

			setActive( resolveActiveSlug() );
		},
		{
			rootMargin: `-${ HEADER_OFFSET }px 0px -60% 0px`,
			threshold: [ 0, 1 ],
		}
	);

	headings.forEach( ( heading ) => observer.observe( heading ) );
	setActive( resolveActiveSlug() );
};

const initToc = () => {
	const roots = Array.from( document.querySelectorAll( TOC_ROOT_SELECTOR ) );

	if ( ! roots.length ) {
		return;
	}

	roots.forEach( bindTocRoot );
	document
		.querySelectorAll( FLOATING_TOC_SELECTOR )
		.forEach( bindFloatingToc );
	initScrollSpy( roots );
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initToc, { once: true } );
} else {
	initToc();
}

document.addEventListener( 'keydown', ( event ) => {
	if ( event.key !== 'Escape' ) {
		return;
	}

	document.querySelectorAll( FLOATING_TOC_SELECTOR ).forEach( ( details ) => {
		closeFloatingToc( details );
	} );
} );
