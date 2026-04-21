<?php
/**
 * Theme Customizer settings for Proger Blog.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

namespace ProgerBlog\Customizer;

const ALLOWED_IMAGE_MIMES = [
	'image/png',
	'image/jpeg',
	'image/svg+xml',
	'image/webp',
];

function sanitize_checkbox(mixed $value): bool {
	return (bool) rest_sanitize_boolean($value);
}

function sanitize_image(mixed $value): string {
	$url = esc_url_raw((string) $value);
	if ('' === $url) {
		return '';
	}
	$type = wp_check_filetype($url);
	if (! isset($type['type']) || ! in_array($type['type'], ALLOWED_IMAGE_MIMES, true)) {
		return '';
	}
	return $url;
}

add_action('customize_register', static function (\WP_Customize_Manager $wp_customize): void {
	$wp_customize->add_section('proger_theme_options', [
		'title'       => __('Proger Blog', 'proger-blog'),
		'description' => __('Custom settings for the Proger Blog theme.', 'proger-blog'),
		'priority'    => 30,
	]);

	// Background image.
	$wp_customize->add_setting('proger_background_image', [
		'default'           => '',
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_image',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control(new \WP_Customize_Image_Control(
		$wp_customize,
		'proger_background_image',
		[
			'label'   => __('Background image', 'proger-blog'),
			'section' => 'proger_theme_options',
		]
	));

	// Enable TOC.
	$wp_customize->add_setting('proger_enable_toc', [
		'default'           => true,
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_checkbox',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control('proger_enable_toc', [
		'type'    => 'checkbox',
		'label'   => __('Enable Table of Contents on articles', 'proger-blog'),
		'section' => 'proger_theme_options',
	]);

	// Enable Sidebar.
	$wp_customize->add_setting('proger_enable_sidebar', [
		'default'           => true,
		'sanitize_callback' => __NAMESPACE__ . '\\sanitize_checkbox',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control('proger_enable_sidebar', [
		'type'    => 'checkbox',
		'label'   => __('Enable left categories sidebar', 'proger-blog'),
		'section' => 'proger_theme_options',
	]);
});
