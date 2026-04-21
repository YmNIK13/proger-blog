<?php
/**
 * Theme supports + nav menus + i18n.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

namespace ProgerBlog;

add_action('after_setup_theme', static function (): void {
	load_theme_textdomain('proger-blog', get_template_directory() . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	]);
	add_theme_support('responsive-embeds');
	add_theme_support('align-wide');
	add_theme_support('wp-block-styles');
	add_theme_support('custom-logo', [
		'height'               => 64,
		'width'                => 240,
		'flex-height'          => true,
		'flex-width'           => true,
		'unlink-homepage-logo' => false,
	]);

	add_image_size('proger-card', 640, 360, true);
	add_image_size('proger-hero', 1600, 900, true);

	register_nav_menus([
		'primary' => __('Primary menu', 'proger-blog'),
		'sidebar' => __('Sidebar menu', 'proger-blog'),
	]);
});

add_filter('excerpt_length', static fn (): int => 28);
add_filter('excerpt_more', static fn (): string => ' …');
