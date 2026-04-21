<?php
/**
 * Home / blog index — renders hero + articles grid.
 *
 * @package ProgerBlog
 */

get_header(); ?>

<div class="flex flex-1 max-w-screen-2xl mx-auto w-full relative">
	<?php get_sidebar(); ?>

	<main id="main" class="flex-1 lg:ml-64 w-full px-6 md:px-12 py-12 min-h-screen">
		<section class="mb-20 text-center max-w-3xl mx-auto mt-6">
			<h1 class="text-5xl md:text-7xl font-black font-headline tracking-tight text-white mb-6 leading-[1.05]">
				<?php esc_html_e('Master the', 'proger-blog'); ?><br/>
				<span class="text-primary"><?php esc_html_e('Machine.', 'proger-blog'); ?></span>
			</h1>
			<p class="text-lg md:text-xl text-on-surface-variant font-body mb-10 leading-relaxed">
				<?php echo esc_html(get_bloginfo('description') ?: __('Advanced tutorials, system architecture breakdowns and unfiltered thoughts on modern software engineering.', 'proger-blog')); ?>
			</p>
			<?php get_search_form(); ?>
		</section>

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
			<p class="text-outline text-center py-12"><?php esc_html_e('Ще немає статей.', 'proger-blog'); ?></p>
		<?php endif; ?>

		<?php
		$post_tags = get_tags(['orderby' => 'count', 'order' => 'DESC', 'number' => 20]);
		if ($post_tags) :
		?>
			<section class="mt-24 bg-surface-container-low rounded-2xl p-8 md:p-12">
				<h2 class="text-xl font-bold font-headline text-primary mb-6 uppercase tracking-widest font-mono"><?php esc_html_e('Tags', 'proger-blog'); ?></h2>
				<div class="flex flex-wrap gap-2">
					<?php foreach ($post_tags as $tag) : ?>
						<a href="<?php echo esc_url(get_term_link($tag)); ?>" class="font-mono text-xs text-primary bg-surface-container px-3 py-1 rounded-full border border-surface-container-high hover:bg-primary hover:text-on-primary transition-colors">
							#<?php echo esc_html($tag->name); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</main>
</div>

<?php get_footer(); ?>
