const SCROLL_THRESHOLD = 8;

function initHeaderNav() {
	const header = document.querySelector('.site-header');
	if (!header) {
		return;
	}

	const menuToggle = header.querySelector('.site-header__menu-toggle');
	const mobilePanel = document.getElementById('mobile-nav-panel');
	const searchForm = header.querySelector('.site-header__search');
	const searchInput = document.getElementById('header-search');
	const searchToggle = header.querySelector('.site-header__search-toggle');
	const searchClose = header.querySelector('.site-header__search-close');

	let menuOpen = false;
	let searchOpen = Boolean(searchInput?.value.trim());
	let ticking = false;

	const setMenuOpen = (open) => {
		menuOpen = open;
		header.classList.toggle('is-menu-open', open);
		document.documentElement.classList.toggle('is-menu-open', open);

		if (menuToggle) {
			menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		if (mobilePanel) {
			mobilePanel.hidden = !open;
		}

		if (open) {
			const firstLink = mobilePanel?.querySelector('a, button');
			firstLink?.focus();
		}
	};

	const setSearchOpen = (open, { focusToggle = false } = {}) => {
		searchOpen = open;
		searchForm?.classList.toggle('is-open', open);
		searchToggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
		if (searchInput) {
			searchInput.disabled = !open;
		}

		if (!open) {
			if (searchInput) {
				searchInput.value = '';
			}
			searchInput?.blur();
			if (focusToggle) {
				searchToggle?.focus();
			}
		}
	};

	const updateScrollState = () => {
		if (ticking) {
			return;
		}

		ticking = true;
		requestAnimationFrame(() => {
			header.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
			ticking = false;
		});
	};

	setMenuOpen(false);
	setSearchOpen(searchOpen);
	updateScrollState();

	menuToggle?.addEventListener('click', () => {
		setMenuOpen(!menuOpen);
	});

	searchToggle?.addEventListener('click', () => {
		if (!searchOpen) {
			setSearchOpen(true);
			requestAnimationFrame(() => searchInput?.focus());
		}
	});

	searchClose?.addEventListener('click', () => {
		setSearchOpen(false, { focusToggle: true });
	});

	searchForm?.addEventListener('submit', (event) => {
		if (!searchInput || searchInput.value.trim() !== '') {
			return;
		}

		event.preventDefault();
		setSearchOpen(true);
		searchInput.focus();
	});

	window.addEventListener('scroll', updateScrollState, { passive: true });

	window.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') {
			return;
		}

		const hadSearchOpen = searchOpen;
		const hadMenuOpen = menuOpen;
		setMenuOpen(false);
		setSearchOpen(false);
		if (hadSearchOpen) {
			searchToggle?.focus();
			return;
		}
		if (hadMenuOpen) {
			menuToggle?.focus();
		}
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initHeaderNav, { once: true });
} else {
	initHeaderNav();
}
