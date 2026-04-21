<?php
/**
 * Proger Blog — bootstrap.
 *
 * Classic theme entry: theme.json supplies design tokens to Gutenberg,
 * but rendering uses classic PHP templates (header.php, single.php, etc.)
 * so that conventional PHP runs without block-template limitations.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

define('PROGER_BLOG_VERSION', '1.0.0');
define('PROGER_BLOG_PATH', trailingslashit(get_template_directory()));
define('PROGER_BLOG_URI', trailingslashit(get_template_directory_uri()));

require_once PROGER_BLOG_PATH . 'inc/theme-support.php';
require_once PROGER_BLOG_PATH . 'inc/assets.php';
require_once PROGER_BLOG_PATH . 'inc/helpers.php';
require_once PROGER_BLOG_PATH . 'inc/toc-anchors.php';
require_once PROGER_BLOG_PATH . 'inc/blocks.php';
require_once PROGER_BLOG_PATH . 'inc/customizer.php';
require_once PROGER_BLOG_PATH . 'inc/theme-mods-output.php';
