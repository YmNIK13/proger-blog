<?php
/**
 * Search results.
 *
 * @package ProgerBlog
 */

get_header(); ?>

<div class="flex flex-1 max-w-screen-2xl mx-auto w-full relative">
	<?php get_sidebar(); ?>

	<?php $sidebar_offset = get_theme_mod('proger_enable_sidebar', true) ? ' lg:ml-64' : ''; ?>

	<main id="main" class="flex-1 w-full px-6 md:px-12 py-12 min-h-screen<?php echo esc_attr($sidebar_offset); ?>">
		<header class="mb-12 max-w-3xl">
			<p class="font-mono text-xs text-primary uppercase tracking-widest mb-3"><?php esc_html_e('Search', 'proger-blog'); ?></p>
			<h1 class="text-4xl md:text-5xl font-black font-headline tracking-tight text-white mb-6">
				<?php printf(esc_html__('Results for "%s"', 'proger-blog'), esc_html(get_search_query())); ?>
			</h1>
			<?php get_search_form(); ?>
		</header>

		<?php if (have_posts()) : ?>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
				<?php while (have_posts()) : the_post();
					proger_blog_render_article_card(get_the_ID());
				endwhile; ?>
			</div>

			<?php
			global $wp_query;
			if ($wp_query->max_num_pages > 1) :
			?>
				<nav class="mt-16 flex items-center justify-center gap-3 font-mono text-sm text-on-surface-variant" aria-label="<?php esc_attr_e('Pagination', 'proger-blog'); ?>">
					<?php echo paginate_links([
						'prev_text' => __('← Prev', 'proger-blog'),
						'next_text' => __('Next →', 'proger-blog'),
						'mid_size'  => 2,
					]); ?>
				</nav>
			<?php endif; ?>
		<?php else : ?>
			<p class="text-outline text-center py-12"><?php esc_html_e('За вашим запитом нічого не знайдено.', 'proger-blog'); ?></p>
		<?php endif; ?>
	</main>
</div>

<?php get_footer(); ?>
