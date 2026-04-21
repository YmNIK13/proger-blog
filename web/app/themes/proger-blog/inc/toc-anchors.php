<?php
/**
 * TOC anchor injection + slug helper.
 *
 * Adds id="…" to h1–h4 in post content so TOC links resolve. The same
 * slug algorithm is reused by blocks/toc/render.php so the two stay in sync.
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

namespace ProgerBlog\Toc;

/**
 * Convert heading text to a safe anchor slug (supports Cyrillic via sanitize_title).
 *
 * @param string             $text
 * @param array<string, int> $used Tracks seen slugs to disambiguate duplicates.
 */
function generate_slug(string $text, array &$used): string {
	$slug = sanitize_title($text);
	if ('' === $slug) {
		$slug = 'section';
	}

	if (! isset($used[$slug])) {
		$used[$slug] = 1;
		return $slug;
	}

	$used[$slug]++;
	return $slug . '-' . $used[$slug];
}

/**
 * Extract headings from HTML content.
 *
 * @return array<int, array{level:int, text:string, slug:string}>
 */
function extract_headings(string $html, int $max_level = 4): array {
	if ('' === trim($html)) {
		return [];
	}

	$headings = [];
	$used     = [];
	$pattern  = '#<h([1-' . max(1, min(6, $max_level)) . '])\b[^>]*>(.*?)</h\1>#is';

	if (! preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
		return $headings;
	}

	foreach ($matches as $m) {
		$level = (int) $m[1];
		$text  = trim(wp_strip_all_tags($m[2]));
		if ('' === $text) {
			continue;
		}
		$headings[] = [
			'level' => $level,
			'text'  => $text,
			'slug'  => generate_slug($text, $used),
		];
	}

	return $headings;
}

/**
 * Inject id="…" into h1–h4 that don't have one yet.
 */
function inject_ids(string $content): string {
	if ('' === trim($content) || ! class_exists('WP_HTML_Tag_Processor')) {
		return $content;
	}

	$used      = [];
	$processor = new \WP_HTML_Tag_Processor($content);
	$levels    = ['H1', 'H2', 'H3', 'H4'];

	while ($processor->next_tag()) {
		$tag = $processor->get_tag();
		if (! in_array($tag, $levels, true)) {
			continue;
		}
		if ($processor->get_attribute('id')) {
			// Preserve author-defined anchors; still reserve the slug to avoid collisions later.
			$used[(string) $processor->get_attribute('id')] = 1;
			continue;
		}

		// We cannot read inner text from the Tag Processor, so fall back to the
		// processor's current bookmarking to slice the source. Simpler and reliable:
		// parse inner text via regex scoped to this tag.
		$html_from_here = substr($content, $processor->get_updated_html() ? 0 : 0);
		break; // We'll do regex pass below.
	}

	// Regex pass: fast enough for post content and handles nested inline tags.
	$used = [];
	return preg_replace_callback(
		'#<(h[1-4])([^>]*)>(.*?)</\1>#is',
		static function (array $m) use (&$used): string {
			$attrs = $m[2];
			if (preg_match('/\bid=["\'][^"\']+["\']/i', $attrs)) {
				return $m[0];
			}
			$text = trim(wp_strip_all_tags($m[3]));
			if ('' === $text) {
				return $m[0];
			}
			$slug = generate_slug($text, $used);
			return sprintf('<%1$s id="%2$s"%3$s>%4$s</%1$s>', $m[1], esc_attr($slug), $attrs, $m[3]);
		},
		$content
	) ?? $content;
}

if (function_exists('add_filter')) {
	add_filter('the_content', __NAMESPACE__ . '\\inject_ids', 9);
}
