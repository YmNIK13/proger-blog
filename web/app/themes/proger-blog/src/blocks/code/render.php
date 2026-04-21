<?php
/**
 * Server render for proger-blog/code.
 *
 * @var array              $attributes
 * @var string             $content
 * @var WP_Block           $block
 */

declare(strict_types=1);

$language = isset($attributes['language']) && is_string($attributes['language'])
	? preg_replace('/[^a-z0-9+-]/i', '', $attributes['language'])
	: 'plaintext';

$filename      = isset($attributes['filename']) ? trim((string) $attributes['filename']) : '';
$show_numbers  = ! empty($attributes['showLineNumbers']);
$raw           = isset($attributes['content']) ? (string) $attributes['content'] : '';
$code_content  = wp_specialchars_decode($raw, ENT_QUOTES);
$language_slug = $language ?: 'plaintext';

$classes = ['proger-code', 'proger-code--lang-' . strtolower($language_slug)];
if ($show_numbers) {
	$classes[] = 'proger-code--line-numbers';
}

$wrapper_attrs = get_block_wrapper_attributes([
	'class'                   => implode(' ', $classes),
	'data-wp-interactive'     => 'proger/code',
	'data-language'           => $language_slug,
]);
?>
<figure <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<header class="proger-code__header">
		<span class="proger-code__lang"><?php echo esc_html(strtoupper($language_slug)); ?></span>
		<?php if ('' !== $filename) : ?>
			<span class="proger-code__filename"><?php echo esc_html($filename); ?></span>
		<?php endif; ?>
		<button
			type="button"
			class="proger-code__copy"
			data-wp-on--click="actions.copy"
			data-wp-bind--aria-label="state.copyLabel"
			aria-label="<?php esc_attr_e('Копіювати код', 'proger-blog'); ?>">
			<span class="proger-code__copy-icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
					<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
				</svg>
			</span>
			<span class="proger-code__copy-label" data-wp-text="state.copyLabel"><?php esc_html_e('Copy', 'proger-blog'); ?></span>
		</button>
	</header>
	<pre class="proger-code__pre"><code class="language-<?php echo esc_attr($language_slug); ?>" data-wp-text="state.content"><?php echo esc_html($code_content); ?></code></pre>
</figure>
<?php
