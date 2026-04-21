import { store, getElement } from '@wordpress/interactivity';

const HEADER_OFFSET = 80;
const prefersReduced = () =>
	typeof window.matchMedia === 'function' &&
	window.matchMedia('(prefers-reduced-motion: reduce)').matches;

store('proger/toc', {
	actions: {
		onClick(event) {
			event.preventDefault();
			const { ref } = getElement();
			const targetSlug = ref.getAttribute('data-target');
			if (!targetSlug) return;
			const target = document.getElementById(targetSlug);
			if (!target) return;

			const top =
				target.getBoundingClientRect().top + window.scrollY - HEADER_OFFSET;
			window.scrollTo({
				top,
				behavior: prefersReduced() ? 'auto' : 'smooth',
			});

			const floatingToc = ref.closest('.single-floating-toc');
			if (floatingToc instanceof HTMLDetailsElement) {
				floatingToc.removeAttribute('open');
			}

			history.replaceState(null, '', `#${targetSlug}`);
			const prev = target.getAttribute('tabindex');
			target.setAttribute('tabindex', '-1');
			target.focus({ preventScroll: true });
			if (prev === null) {
				setTimeout(() => target.removeAttribute('tabindex'), 300);
			}
		},
	},
	callbacks: {
		init() {
			const { ref } = getElement();
			const links = Array.from(ref.querySelectorAll('.toc__link'));
			const floatingToc = ref.closest('.single-floating-toc');

			if (floatingToc instanceof HTMLDetailsElement) {
				const closeFloatingToc = () => floatingToc.removeAttribute('open');

				if (!floatingToc.dataset.tocBound) {
					floatingToc.dataset.tocBound = 'true';

					floatingToc.addEventListener('click', (event) => {
						const closeTarget =
							event.target instanceof Element
								? event.target.closest('[data-toc-close]')
								: null;

						if (!closeTarget || !floatingToc.contains(closeTarget)) {
							return;
						}

						event.preventDefault();
						closeFloatingToc();
					});

					document.addEventListener('keydown', (event) => {
						if (event.key === 'Escape') {
							closeFloatingToc();
						}
					});
				}
			}

			if (!links.length || !('IntersectionObserver' in window)) return;

			const slugToLink = new Map(
				links.map((a) => [a.getAttribute('data-target'), a]),
			);
			const headings = links
				.map((a) => document.getElementById(a.getAttribute('data-target')))
				.filter(Boolean);

			let activeSlug = null;
			const setActive = (slug) => {
				if (slug === activeSlug) return;
				if (activeSlug) {
					const prev = slugToLink.get(activeSlug);
					prev?.classList.remove('is-active');
					prev?.removeAttribute('aria-current');
				}
				if (slug) {
					const next = slugToLink.get(slug);
					next?.classList.add('is-active');
					next?.setAttribute('aria-current', 'location');
				}
				activeSlug = slug;
			};

			const visible = new Set();
			const io = new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						const id = entry.target.id;
						if (entry.isIntersecting) visible.add(id);
						else visible.delete(id);
					});
					const top = headings.find((h) => visible.has(h.id));
					setActive(top ? top.id : null);
				},
				{
					rootMargin: `-${HEADER_OFFSET}px 0px -60% 0px`,
					threshold: [0, 1],
				},
			);

			headings.forEach((h) => io.observe(h));
		},
	},
});
