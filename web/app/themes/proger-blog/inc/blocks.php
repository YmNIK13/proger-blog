<?php
/**
 * Register custom blocks shipped with the theme.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

namespace ProgerBlog\Blocks;

add_action('init', static function (): void {
	// @wordpress/scripts build copies block.json (with rewritten asset paths)
	// plus render.php into build/blocks/<name>/. Register from there so that
	// referenced compiled assets resolve correctly.
	$dirs = [
		PROGER_BLOG_PATH . 'build/blocks/code',
		PROGER_BLOG_PATH . 'build/blocks/toc',
	];

	foreach ($dirs as $dir) {
		if (is_dir($dir) && file_exists($dir . '/block.json')) {
			register_block_type_from_metadata($dir);
		}
	}
});
