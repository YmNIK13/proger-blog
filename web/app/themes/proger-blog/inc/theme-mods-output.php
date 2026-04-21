<?php
/**
 * Pipe Customizer theme_mods into inline CSS + body classes.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

namespace ProgerBlog\ThemeMods;

add_filter('body_class', static function (array $classes): array {
	if (! get_theme_mod('proger_enable_toc', true)) {
		$classes[] = 'has-no-toc';
	}
	if (! get_theme_mod('proger_enable_sidebar', true)) {
		$classes[] = 'has-no-sidebar';
	}
	return $classes;
});

add_action('wp_enqueue_scripts', static function (): void {
	$bg = esc_url_raw((string) get_theme_mod('proger_background_image', ''));
	if ('' === $bg) {
		return;
	}

	$css = sprintf(':root{--proger-body-bg-image:url("%s");}', esc_url($bg));
	wp_add_inline_style('proger-blog-main', $css);
}, 30);
