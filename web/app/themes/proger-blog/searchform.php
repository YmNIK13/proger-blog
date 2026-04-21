<?php
/**
 * Search form partial (used by get_search_form()).
 *
 * @package ProgerBlog
 */
$search_id = 'proger-search-' . wp_unique_id();
?>
<form class="relative max-w-xl mx-auto" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
	<div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
		<?php echo proger_blog_icon('search', 'text-outline'); ?>
	</div>
	<label for="<?php echo esc_attr($search_id); ?>" class="sr-only"><?php esc_html_e('Search', 'proger-blog'); ?></label>
	<input
		id="<?php echo esc_attr($search_id); ?>"
		type="search"
		name="s"
		class="w-full bg-surface-container-low border border-outline-variant/30 text-white rounded-lg pl-12 pr-4 py-4 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors font-body placeholder:text-outline"
		placeholder="<?php esc_attr_e('Search articles, tags, or concepts…', 'proger-blog'); ?>"
		value="<?php echo esc_attr(get_search_query()); ?>"
	/>
</form>
