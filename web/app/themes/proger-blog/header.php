<?php
/**
 * Site header — sticky backdrop-blur nav with logo + main menu + search.
 *
 * @package ProgerBlog
 */
?><!doctype html>
<html <?php language_attributes(); ?> class="dark">
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class('bg-background text-on-background min-h-screen flex flex-col antialiased'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e('Пропустити до контенту', 'proger-blog'); ?></a>

<nav
	class="site-header bg-slate-950/70 backdrop-blur-xl sticky top-0 z-50 w-full"
	role="banner"
>
	<div class="flex items-center w-full gap-6 px-8 py-4 max-w-[1800px] mx-auto">
		<div class="flex items-center gap-8 md:flex-1 md:justify-end">
			<?php if (has_custom_logo()) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="text-2xl font-black tracking-tighter text-white uppercase font-headline hover:text-primary transition-colors" href="<?php echo esc_url(home_url('/')); ?>">
					<?php echo esc_html(get_bloginfo('name')); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="hidden md:flex md:flex-1 items-center justify-center gap-8">
			<?php
			wp_nav_menu([
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'flex items-center gap-8 list-none m-0 p-0',
				'fallback_cb'    => 'proger_blog_fallback_menu',
				'depth'          => 2,
				'walker'         => new Proger_Blog_Nav_Walker(),
			]);
			?>
		</div>

		<div class="ml-auto flex items-center gap-4 md:flex-1 md:justify-end">
			<form
				class="site-header__search hidden md:flex items-center justify-end"
				role="search"
				method="get"
				action="<?php echo esc_url(home_url('/')); ?>"
			>
				<label for="header-search" class="sr-only"><?php esc_html_e('Пошук', 'proger-blog'); ?></label>
				<button
					type="button"
					class="site-header__search-toggle inline-flex text-primary hover:bg-white/5 p-2 rounded-full transition-all duration-fast ease-standard"
					aria-controls="header-search"
					aria-expanded="false"
					aria-label="<?php esc_attr_e('Відкрити пошук', 'proger-blog'); ?>"
				>
					<?php echo proger_blog_icon('search'); ?>
				</button>
				<div class="site-header__search-shell">
					<input
						id="header-search"
						type="search"
						name="s"
						required
						class="site-header__search-input"
						placeholder="<?php esc_attr_e('Введіть запит…', 'proger-blog'); ?>"
						value="<?php echo esc_attr(get_search_query()); ?>"
					/>
					<div class="site-header__search-actions">
						<button
							type="submit"
							class="site-header__search-submit inline-flex transition-all duration-fast ease-standard"
							aria-label="<?php esc_attr_e('Шукати', 'proger-blog'); ?>"
						>
							<?php echo proger_blog_icon('search', 'site-header__search-icon'); ?>
						</button>
						<button
							type="button"
							class="site-header__search-close inline-flex transition-all duration-fast ease-standard"
							aria-controls="header-search"
							aria-label="<?php esc_attr_e('Закрити пошук', 'proger-blog'); ?>"
						>
							<?php echo proger_blog_icon('close', 'site-header__search-icon'); ?>
						</button>
					</div>
				</div>
			</form>
			<button
				type="button"
				class="site-header__menu-toggle md:hidden text-primary hover:bg-white/5 p-2 rounded-full transition-all duration-fast ease-standard"
				aria-controls="mobile-nav-panel"
				aria-expanded="false"
			>
				<span class="sr-only"><?php esc_attr_e('Меню', 'proger-blog'); ?></span>
				<?php echo proger_blog_icon('menu'); ?>
			</button>
		</div>
	</div>

	<div
		id="mobile-nav-panel"
		class="fixed inset-x-0 top-[68px] bg-surface-container p-6 border-t border-outline-variant/20 z-40"
		hidden
		aria-label="<?php esc_attr_e('Мобільне меню', 'proger-blog'); ?>"
	>
		<?php
		wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'flex flex-col gap-4 list-none m-0 p-0',
			'fallback_cb'    => 'proger_blog_fallback_menu',
			'depth'          => 2,
		]);
		?>
	</div>
</nav>
