<?php
/**
 * Sidebar — sticky left panel driven by a dedicated WP menu.
 *
 * @package ProgerBlog
 */

if (! get_theme_mod('proger_enable_sidebar', true)) {
	return;
}
?>

<aside class="sidebar-scrollbar bg-surface-container h-[calc(100vh-5rem)] w-64 hidden lg:flex flex-col p-6 fixed left-0 top-20 z-40 overflow-y-auto overflow-x-hidden" aria-label="<?php esc_attr_e('Категорії', 'proger-blog'); ?>">
	<nav>
		<?php
		wp_nav_menu([
			'theme_location' => 'sidebar',
			'container'      => false,
			'menu_class'     => 'flex flex-col gap-2 list-none m-0 p-0',
			'fallback_cb'    => 'proger_blog_sidebar_fallback_menu',
			'depth'          => 1,
			'walker'         => new Proger_Blog_Sidebar_Nav_Walker(),
		]);
		?>
	</nav>
</aside>
