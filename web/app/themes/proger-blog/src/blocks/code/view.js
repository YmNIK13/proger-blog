/* eslint-env browser */

const PRISM_CDN = 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0';
const CODE_BLOCK_SELECTOR = '.proger-code';
const COPY_RESET_DELAY = 2000;

const LANG_DEPS = {
	typescript: [ 'javascript' ],
	scss: [ 'css' ],
	php: [ 'markup' ],
};

const loadedLangs = new Set();
const loadScript = ( src ) =>
	new Promise( ( resolve, reject ) => {
		const s = document.createElement( 'script' );
		s.src = src;
		s.async = false;
		s.onload = resolve;
		s.onerror = reject;
		document.head.appendChild( s );
	} );

async function ensurePrism() {
	if ( window.Prism ) {
		return window.Prism;
	}

	await loadScript( `${ PRISM_CDN }/prism.min.js` );
	window.Prism.manual = true;
	return window.Prism;
}

async function ensureLanguage( lang ) {
	if ( ! lang || lang === 'plaintext' || loadedLangs.has( lang ) ) {
		return;
	}

	const Prism = await ensurePrism();
	const deps = LANG_DEPS[ lang ] ?? [];
	for ( const dep of deps ) {
		await ensureLanguage( dep );
	}
	if ( ! Prism.languages[ lang ] ) {
		await loadScript( `${ PRISM_CDN }/components/prism-${ lang }.min.js` );
	}
	loadedLangs.add( lang );
}

async function copyText( text ) {
	if ( navigator.clipboard?.writeText ) {
		await navigator.clipboard.writeText( text );
		return;
	}

	const textarea = document.createElement( 'textarea' );
	textarea.value = text;
	textarea.setAttribute( 'readonly', '' );
	textarea.style.position = 'absolute';
	textarea.style.left = '-9999px';
	document.body.appendChild( textarea );
	textarea.select();
	document.execCommand( 'copy' );
	textarea.remove();
}

function setCopyState( button, label, stateClass ) {
	const root = button.closest( CODE_BLOCK_SELECTOR );
	const labelNode = button.querySelector( '.proger-code__copy-label' );
	const defaultLabel = button.dataset.copyDefaultLabel || 'Copy';

	root?.classList.remove( 'is-copied', 'is-error' );
	if ( stateClass ) {
		root?.classList.add( stateClass );
	}

	if ( labelNode ) {
		labelNode.textContent = label;
	}

	button.setAttribute( 'aria-label', label );

	window.setTimeout( () => {
		root?.classList.remove( 'is-copied', 'is-error' );
		if ( labelNode ) {
			labelNode.textContent = defaultLabel;
		}
		button.setAttribute( 'aria-label', defaultLabel );
	}, COPY_RESET_DELAY );
}

async function highlightBlock( root ) {
	if (
		! ( root instanceof HTMLElement ) ||
		root.dataset.highlighted === 'true'
	) {
		return;
	}

	root.dataset.highlighted = 'true';

	const lang = root.dataset.language ?? 'plaintext';
	if ( lang === 'plaintext' ) {
		return;
	}

	await ensureLanguage( lang );

	const codeElement = root.querySelector( 'code' );
	if ( codeElement && window.Prism?.languages?.[ lang ] ) {
		window.Prism.highlightElement( codeElement );
	}
}

function bindCodeBlock( root ) {
	if (
		! ( root instanceof HTMLElement ) ||
		root.dataset.codeReady === 'true'
	) {
		return;
	}

	root.dataset.codeReady = 'true';

	const copyButton = root.querySelector( '.proger-code__copy' );
	if ( copyButton instanceof HTMLButtonElement ) {
		copyButton.addEventListener( 'click', async ( event ) => {
			event.preventDefault();

			const code = root.querySelector( 'code' )?.innerText ?? '';
			const successLabel =
				copyButton.dataset.copySuccessLabel || 'Скопійовано';
			const errorLabel = copyButton.dataset.copyErrorLabel || 'Помилка';

			try {
				await copyText( code );
				setCopyState( copyButton, successLabel, 'is-copied' );
			} catch ( _error ) {
				setCopyState( copyButton, errorLabel, 'is-error' );
			}
		} );
	}

	void highlightBlock( root );
}

function initCodeBlocks() {
	document.querySelectorAll( CODE_BLOCK_SELECTOR ).forEach( bindCodeBlock );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initCodeBlocks, {
		once: true,
	} );
} else {
	initCodeBlocks();
}
