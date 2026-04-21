<?php
/**
 * Sidebar — category tree with Material icons, sticky left panel.
 *
 * @package ProgerBlog
 */

if (! get_theme_mod('proger_enable_sidebar', true)) {
	return;
}

$proger_category_icons = [
	'latest'    => 'speed',
	'trending'  => 'bolt',
	'back-end'  => 'database',
	'backend'   => 'database',
	'front-end' => 'terminal',
	'frontend'  => 'terminal',
	'devops'    => 'cloud',
	'db'        => 'database',
	'git'       => 'commit',
	'lang'      => 'translate',
	'phpstorm'  => 'code',
	'server'    => 'dns',
	'ubuntu'    => 'desktop_linux',
	'wordpress' => 'language',
	'tutorials' => 'school',
];

$proger_icon_for = static function (string $slug) use ($proger_category_icons): string {
	return $proger_category_icons[strtolower($slug)] ?? 'tag';
};

$active_term = is_category() ? get_queried_object() : null;
$active_id   = ($active_term instanceof WP_Term) ? (int) $active_term->term_id : null;

$terms = get_categories([
	'hide_empty' => true,
	'orderby'    => 'name',
	'parent'     => 0,
]);

if (empty($terms)) {
	return;
}
?>

<aside class="bg-surface-container h-[calc(100vh-5rem)] w-64 rounded-r-2xl hidden lg:flex flex-col gap-4 p-6 fixed left-0 top-20 z-40 overflow-y-auto" aria-label="<?php esc_attr_e('Категорії', 'proger-blog'); ?>">
	<div class="mb-6">
		<h2 class="text-primary font-bold font-mono text-sm uppercase tracking-widest mb-1"><?php esc_html_e('Categories', 'proger-blog'); ?></h2>
		<p class="text-on-surface-variant text-xs"><?php esc_html_e('Filter by technology', 'proger-blog'); ?></p>
	</div>

	<nav class="flex flex-col gap-2">
		<a class="side-link <?php echo (is_home() || is_front_page()) ? 'is-active' : ''; ?>" href="<?php echo esc_url(home_url('/')); ?>"<?php echo (is_home() || is_front_page()) ? ' aria-current="page"' : ''; ?>>
			<?php echo proger_blog_icon('speed'); ?>
			<?php esc_html_e('Latest', 'proger-blog'); ?>
		</a>

		<?php foreach ($terms as $term) :
			$icon      = $proger_icon_for($term->slug);
			$is_active = $active_id === (int) $term->term_id;
		?>
			<a
				class="side-link <?php echo $is_active ? 'is-active' : ''; ?>"
				href="<?php echo esc_url(get_term_link($term)); ?>"
				<?php echo $is_active ? 'aria-current="page"' : ''; ?>
				data-slug="<?php echo esc_attr($term->slug); ?>"
			>
				<?php echo proger_blog_icon($icon); ?>
				<?php echo esc_html($term->name); ?>
			</a>
		<?php endforeach; ?>
	</nav>
</aside>
