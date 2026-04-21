<?php
/**
 * Template helpers used across classic templates (index.php, single.php, etc.).
 *
 * @package ProgerBlog
 */

declare(strict_types=1);

/**
 * Render a Material Symbol icon without ligature text in the DOM.
 */
function proger_blog_icon(string $icon, string $classes = '', array $attrs = []): string {
	if ('' === trim($icon)) {
		return '';
	}

	$attributes = array_merge(
		[
			'class'       => trim('material-symbols-outlined ' . $classes),
			'aria-hidden' => 'true',
			'data-icon'   => $icon,
		],
		$attrs
	);

	$parts = [];
	foreach ($attributes as $name => $value) {
		if (null === $value || false === $value || '' === $value) {
			continue;
		}

		if (true === $value) {
			$parts[] = esc_attr((string) $name);
			continue;
		}

		$parts[] = sprintf('%s="%s"', esc_attr((string) $name), esc_attr((string) $value));
	}

	return sprintf('<span %s></span>', implode(' ', $parts));
}

/**
 * Normalize imported text fragments so previews wrap like regular prose.
 */
function proger_blog_normalize_preview_text(string $text): string {
	$text = wp_strip_all_tags($text);
	$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, get_bloginfo('charset') ?: 'UTF-8');
	$text = preg_replace('/[\x{00A0}\x{1680}\x{180E}\x{2000}-\x{200D}\x{202F}\x{205F}\x{2060}\x{3000}\x{FEFF}]+/u', ' ', $text) ?? $text;
	$text = preg_replace('/\s+/u', ' ', $text) ?? $text;

	return trim($text);
}

/**
 * Render the white "matter" article card for a post.
 */
function proger_blog_render_article_card(int $post_id): void {
	$permalink = get_permalink($post_id);
	$title     = get_the_title($post_id);
	$excerpt   = proger_blog_normalize_preview_text((string) get_the_excerpt($post_id));
	$date      = get_the_date('M j, Y', $post_id);
	$tags      = get_the_tags($post_id);
	$cats      = get_the_category($post_id);
	$primary_cat = $cats ? $cats[0] : null;
	$code_preview = proger_blog_extract_code_preview($post_id);
	?>
	<article class="card-matter group">
		<div class="mb-4 flex justify-between items-start gap-2">
			<div class="flex flex-wrap gap-2">
				<?php if ($primary_cat) : ?>
					<a href="<?php echo esc_url(get_term_link($primary_cat)); ?>" class="text-xs font-mono bg-surface-container text-primary px-2 py-1 rounded hover:bg-surface-container-high transition-colors">
						<?php echo esc_html($primary_cat->name); ?>
					</a>
				<?php endif; ?>
				<?php if ($tags) : foreach (array_slice($tags, 0, 2) as $tag) : ?>
					<a href="<?php echo esc_url(get_term_link($tag)); ?>" class="text-xs font-mono bg-surface-container text-primary px-2 py-1 rounded hover:bg-surface-container-high transition-colors">
						<?php echo esc_html($tag->name); ?>
					</a>
				<?php endforeach; endif; ?>
			</div>
			<span class="text-xs font-mono text-slate-500 whitespace-nowrap"><?php echo esc_html($date); ?></span>
		</div>

		<h3 class="text-2xl font-bold font-headline tracking-tight text-slate-950 mb-3 group-hover:text-primary-container transition-colors leading-tight">
			<a href="<?php echo esc_url($permalink); ?>" class="no-underline hover:text-primary-container">
				<?php echo esc_html($title); ?>
			</a>
		</h3>

		<?php if ($excerpt) : ?>
			<p class="text-slate-700 font-body mb-6 flex-1 leading-relaxed break-words">
				<?php echo esc_html(wp_trim_words($excerpt, 28, '…')); ?>
			</p>
		<?php endif; ?>

		<?php if ($code_preview !== null) : ?>
			<div class="bg-on-primary-container rounded-lg p-4 font-mono text-xs relative overflow-hidden mt-auto border border-surface-container-high/30">
				<span class="absolute top-2 right-2 font-mono text-[10px] text-outline"><?php echo esc_html($code_preview['language']); ?></span>
				<pre class="text-outline-variant overflow-hidden leading-relaxed m-0"><code><?php echo esc_html($code_preview['snippet']); ?></code></pre>
			</div>
		<?php elseif (has_post_thumbnail($post_id)) : ?>
			<a href="<?php echo esc_url($permalink); ?>" class="block w-full h-40 bg-surface-container rounded-lg mt-auto overflow-hidden border border-outline-variant/15">
				<?php echo get_the_post_thumbnail($post_id, 'proger-card', [
					'class'    => 'w-full h-full object-cover opacity-90 hover:opacity-100 transition-opacity',
					'loading'  => 'lazy',
					'decoding' => 'async',
				]); ?>
			</a>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Extract a short code snippet from the first `proger-blog/code` block in a post.
 *
 * @return array{snippet: string, language: string}|null
 */
function proger_blog_extract_code_preview(int $post_id, int $max_lines = 5): ?array {
	$post = get_post($post_id);
	if (! $post instanceof WP_Post) {
		return null;
	}

	$blocks = parse_blocks($post->post_content);
	foreach ($blocks as $block) {
		if (($block['blockName'] ?? '') !== 'proger-blog/code') {
			continue;
		}
		$content  = (string) ($block['attrs']['content'] ?? '');
		$language = (string) ($block['attrs']['language'] ?? 'plaintext');
		if ('' === trim($content)) {
			continue;
		}
		$lines = preg_split('/\r\n|\r|\n/', $content) ?: [];
		$truncated = array_slice($lines, 0, $max_lines);
		if (count($lines) > $max_lines) {
			$truncated[] = '…';
		}
		return [
			'snippet'  => implode("\n", $truncated),
			'language' => strtolower($language),
		];
	}

	return null;
}

/**
 * Extract the normalized first word from a sidebar label for icon matching.
 */
function proger_blog_sidebar_icon_key(string $label): string {
	$label = trim(wp_strip_all_tags($label));
	if ('' === $label) {
		return '';
	}

	$words = preg_split('/\s+/u', $label) ?: [];
	$key   = $words[0] ?? '';
	$key   = preg_replace('/^[^\p{L}\p{N}-]+|[^\p{L}\p{N}-]+$/u', '', $key) ?? $key;

	if (function_exists('mb_strtolower')) {
		return mb_strtolower($key, 'UTF-8');
	}

	return strtolower($key);
}

/**
 * Resolve a Material Symbol icon name for a sidebar item label.
 */
function proger_blog_sidebar_icon_name(string $label): string {
	static $icons = [
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
		'ubuntu'    => 'terminal',
		'wordpress' => 'language',
		'tutorials' => 'school',
	];

	$key = proger_blog_sidebar_icon_key($label);

	return $icons[$key] ?? 'tag';
}

/**
 * Render a sidebar nav link with an auto-detected icon.
 */
function proger_blog_render_sidebar_link(
	string $label,
	string $url,
	bool $is_current = false,
	array $attrs = []
): string {
	$attributes = array_merge(
		[
			'class' => trim('side-link' . ($is_current ? ' is-active' : '')),
			'href'  => esc_url($url),
		],
		$attrs
	);

	if ($is_current) {
		$attributes['aria-current'] = 'page';
	}

	$parts = [];
	foreach ($attributes as $name => $value) {
		if (null === $value || false === $value || '' === $value) {
			continue;
		}

		if (true === $value) {
			$parts[] = esc_attr((string) $name);
			continue;
		}

		$escaped_value = 'href' === $name ? esc_url((string) $value) : esc_attr((string) $value);
		$parts[]       = sprintf('%s="%s"', esc_attr((string) $name), $escaped_value);
	}

	return sprintf(
		'<a %s>%s%s</a>',
		implode(' ', $parts),
		proger_blog_icon(proger_blog_sidebar_icon_name($label)),
		esc_html($label)
	);
}

/**
 * Fallback menu rendered when no `primary` menu is assigned — lets users see
 * something clickable before they configure Appearance → Menus.
 */
function proger_blog_fallback_menu(): void {
	$categories = get_categories([
		'hide_empty' => true,
		'number'     => 4,
		'orderby'    => 'count',
		'order'      => 'DESC',
	]);

	if (empty($categories)) {
		return;
	}

	echo '<ul class="flex items-center gap-8 list-none m-0 p-0">';
	foreach ($categories as $term) {
		printf(
			'<li><a class="text-on-surface-variant font-medium hover:text-white hover:bg-white/5 transition-all duration-fast ease-standard px-2 py-1 rounded-md" href="%s">%s</a></li>',
			esc_url(get_term_link($term)),
			esc_html($term->name)
		);
	}
	echo '</ul>';
}

/**
 * Fallback sidebar menu rendered until a dedicated sidebar menu is assigned.
 */
function proger_blog_sidebar_fallback_menu(mixed $args = null): void {
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

	echo '<ul class="flex flex-col gap-2 list-none m-0 p-0">';
	echo '<li>';
	echo proger_blog_render_sidebar_link(
		__('Latest', 'proger-blog'),
		home_url('/'),
		is_home() || is_front_page()
	);
	echo '</li>';

	foreach ($terms as $term) {
		echo '<li>';
		echo proger_blog_render_sidebar_link(
			$term->name,
			(string) get_term_link($term),
			$active_id === (int) $term->term_id,
			[
				'data-slug'     => $term->slug,
				'data-icon-key' => proger_blog_sidebar_icon_key($term->name),
			]
		);
		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Navigation walker that emits Tailwind-styled links matching the Stitch template.
 */
if (! class_exists('Proger_Blog_Nav_Walker')) {
	class Proger_Blog_Nav_Walker extends Walker_Nav_Menu {
		public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
			$is_current = in_array('current-menu-item', (array) $item->classes, true)
				|| in_array('current_page_item', (array) $item->classes, true);

			$link_classes = $is_current
				? 'text-white border-b-2 border-primary pb-1 font-medium hover:bg-white/5 transition-all duration-fast ease-standard'
				: 'text-on-surface-variant font-medium hover:text-white hover:bg-white/5 transition-all duration-fast ease-standard px-2 py-1 rounded-md';

			$output .= sprintf(
				'<li><a class="%s" href="%s"%s>%s</a></li>',
				esc_attr($link_classes),
				esc_url($item->url),
				$is_current ? ' aria-current="page"' : '',
				esc_html($item->title)
			);
		}

		public function end_el(&$output, $item, $depth = 0, $args = null) {
			// start_el outputs full <li>…</li>, nothing to close here.
		}
	}
}

/**
 * Navigation walker for the styled sidebar menu with auto-detected icons.
 */
if (! class_exists('Proger_Blog_Sidebar_Nav_Walker')) {
	class Proger_Blog_Sidebar_Nav_Walker extends Walker_Nav_Menu {
		public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
			$classes = (array) ($item->classes ?? []);
			$current_classes = [
				'current-menu-item',
				'current-menu-parent',
				'current-menu-ancestor',
				'current_page_item',
				'current_page_parent',
				'current_page_ancestor',
			];

			$is_current = [] !== array_intersect($current_classes, $classes);

			$output .= sprintf(
				'<li>%s</li>',
				proger_blog_render_sidebar_link(
					(string) $item->title,
					(string) $item->url,
					$is_current,
					[
						'data-icon-key' => proger_blog_sidebar_icon_key((string) $item->title),
					]
				)
			);
		}

		public function end_el(&$output, $item, $depth = 0, $args = null) {
			// start_el outputs the full list item.
		}
	}
}
