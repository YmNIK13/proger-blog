import { store, getContext, getElement } from '@wordpress/interactivity';

const PRISM_CDN = 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0';

const LANG_DEPS = {
	typescript: ['javascript'],
	scss: ['css'],
	php: ['markup'],
};

const loadedLangs = new Set();
const loadScript = (src) =>
	new Promise((resolve, reject) => {
		const s = document.createElement('script');
		s.src = src;
		s.async = false;
		s.onload = resolve;
		s.onerror = reject;
		document.head.appendChild(s);
	});

async function ensurePrism() {
	if (window.Prism) return window.Prism;
	await loadScript(`${PRISM_CDN}/prism.min.js`);
	window.Prism.manual = true;
	return window.Prism;
}

async function ensureLanguage(lang) {
	if (!lang || lang === 'plaintext' || loadedLangs.has(lang)) return;
	const Prism = await ensurePrism();
	const deps = LANG_DEPS[lang] ?? [];
	for (const dep of deps) {
		await ensureLanguage(dep);
	}
	if (!Prism.languages[lang]) {
		await loadScript(`${PRISM_CDN}/components/prism-${lang}.min.js`);
	}
	loadedLangs.add(lang);
}

const { state, actions } = store('proger/code', {
	state: {
		copyLabel: 'Copy',
	},
	actions: {
		*copy(event) {
			event.preventDefault();
			const { ref } = getElement();
			const code = ref.closest('.proger-code')?.querySelector('code')?.innerText ?? '';

			try {
				if (navigator.clipboard?.writeText) {
					yield navigator.clipboard.writeText(code);
				} else {
					const ta = document.createElement('textarea');
					ta.value = code;
					ta.setAttribute('readonly', '');
					ta.style.position = 'absolute';
					ta.style.left = '-9999px';
					document.body.appendChild(ta);
					ta.select();
					document.execCommand('copy');
					ta.remove();
				}
				state.copyLabel = 'Скопійовано';
				ref.closest('.proger-code')?.classList.add('is-copied');
			} catch (_err) {
				state.copyLabel = 'Помилка';
				ref.closest('.proger-code')?.classList.add('is-error');
			}

			setTimeout(() => {
				state.copyLabel = 'Copy';
				ref.closest('.proger-code')?.classList.remove('is-copied', 'is-error');
			}, 2000);
		},
	},
	callbacks: {
		*highlight() {
			const { ref } = getElement();
			const lang = ref.dataset.language ?? 'plaintext';
			if (lang === 'plaintext') return;
			yield ensureLanguage(lang);
			const codeEl = ref.querySelector('code');
			if (codeEl && window.Prism?.languages?.[lang]) {
				window.Prism.highlightElement(codeEl);
			}
		},
	},
});
