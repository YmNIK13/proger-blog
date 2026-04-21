<?php
/**
 * Server render for proger-blog/toc.
 *
 * Extracts h1–h4 from the current post's rendered content, builds a nested
 * nav, and outputs matching schema.org JSON-LD.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

declare(strict_types=1);

if (! function_exists('ProgerBlog\\Toc\\extract_headings')) {
	return;
}

if (! get_theme_mod('proger_enable_toc', true)) {
	return;
}

if (! is_singular() || ! in_the_loop()) {
	$post = get_post();
} else {
	$post = get_post();
}

if (! $post instanceof WP_Post) {
	return;
}

$raw_content = apply_filters('the_content', $post->post_content);
$max_level   = isset($attributes['maxLevel']) ? (int) $attributes['maxLevel'] : 4;
$max_level   = max(1, min(6, $max_level));
$title       = isset($attributes['title']) && $attributes['title'] !== ''
	? (string) $attributes['title']
	: __('Зміст', 'proger-blog');

$headings = \ProgerBlog\Toc\extract_headings($raw_content, $max_level);

if (empty($headings)) {
	return;
}

$build_list = static function (array $items) use (&$build_list): string {
	if (empty($items)) {
		return '';
	}
	$html = '<ol class="toc__list">';
	foreach ($items as $node) {
		$html .= sprintf(
			'<li class="toc__item toc__item--level-%1$d"><a class="toc__link" href="#%2$s" data-target="%2$s">%3$s</a>',
			(int) $node['level'],
			esc_attr($node['slug']),
			esc_html($node['text'])
		);
		$html .= $build_list($node['children'] ?? []);
		$html .= '</li>';
	}
	$html .= '</ol>';
	return $html;
};

$nest = static function (array $flat) use (&$nest): array {
	$out    = [];
	$stack  = [&$out];
	$levels = [0];

	foreach ($flat as $h) {
		$level = $h['level'];
		while (count($levels) > 1 && end($levels) >= $level) {
			array_pop($stack);
			array_pop($levels);
		}
		$current = &$stack[count($stack) - 1];
		$current[] = [
			'level'    => $level,
			'slug'     => $h['slug'],
			'text'     => $h['text'],
			'children' => [],
		];
		$new_ref = &$current[count($current) - 1]['children'];
		$stack[] = &$new_ref;
		$levels[] = $level;
		unset($current, $new_ref);
	}
	return $out;
};

$nested  = $nest($headings);
$listing = $build_list($nested);

$permalink = get_permalink($post);
$items_ld  = [];
$position  = 1;
foreach ($headings as $h) {
	$items_ld[] = [
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => $h['text'],
		'url'      => $permalink . '#' . $h['slug'],
	];
}

$json_ld = wp_json_encode([
	'@context'        => 'https://schema.org',
	'@type'           => 'TableOfContents',
	'name'            => $title,
	'itemListElement' => $items_ld,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

static $toc_ld_emitted = false;

$wrapper_attrs = get_block_wrapper_attributes([
	'class' => 'toc',
]);
?>
<nav <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php echo esc_attr($title); ?>">
	<details class="toc__details" open>
		<summary class="toc__summary"><?php echo esc_html($title); ?></summary>
		<div class="toc__body">
			<?php echo $listing; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</details>
</nav>
<?php if (! $toc_ld_emitted) :
	$toc_ld_emitted = true; ?>
	<script type="application/ld+json"><?php echo $json_ld; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
<?php endif; ?>
<?php
