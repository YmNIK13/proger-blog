<?php
/**
 * Single post — "white card on void" layout with right TOC.
 *
 * @package ProgerBlog
 */

get_header(); ?>

<div class="flex flex-1 max-w-[1800px] mx-auto w-full">
	<?php get_sidebar(); ?>

	<?php while (have_posts()) : the_post(); ?>
		<?php
		$toc_markup = '';
		$has_toc    = false;
		$toc_title  = __('Зміст', 'proger-blog');
		$sidebar_offset = get_theme_mod('proger_enable_sidebar', true) ? ' lg:ml-64' : '';
		$layout_state_class = get_theme_mod('proger_enable_sidebar', true) ? ' single-layout--with-sidebar' : '';
		$main_class = 'single-layout flex-1 px-3 sm:px-4 lg:px-6 xl:px-8 pt-8 pb-28 sm:pt-10 sm:pb-28 lg:py-20 flex flex-col relative min-h-screen items-center' . $sidebar_offset . $layout_state_class;
		$article_class = 'single-article single-matter w-full max-w-[960px] p-4 sm:p-5 lg:p-7 xl:p-10 relative mx-auto';
		$aside_class = 'single-desktop-toc hidden min-[1441px]:block shrink-0 self-start';

		if (get_theme_mod('proger_enable_toc', true)) {
			$toc_markup = trim((string) do_blocks('<!-- wp:proger-blog/toc /-->'));
			$has_toc    = '' !== $toc_markup;
		}

		if ($has_toc) {
			$main_class .= ' single-layout--has-toc';
			$article_class = 'single-article single-article--with-toc single-matter w-full max-w-[960px] p-4 sm:p-5 lg:p-7 relative xl:p-10';
		}
		?>
		<main id="main" class="<?php echo esc_attr($main_class); ?>">
			<article class="<?php echo esc_attr($article_class); ?>">
				<header id="page-top" class="mb-10 sm:mb-12">
					<nav class="flex items-center gap-2 text-slate-600 font-mono text-xs uppercase tracking-wider mb-6 flex-wrap" aria-label="<?php esc_attr_e('Breadcrumbs', 'proger-blog'); ?>">
						<a class="hover:text-primary-container transition-colors" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'proger-blog'); ?></a>
						<?php echo proger_blog_icon('chevron_right', 'text-[16px]'); ?>
						<?php
						$cats = get_the_category();
						if ($cats) : $cat = $cats[0]; ?>
							<a class="hover:text-primary-container transition-colors" href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
							<?php echo proger_blog_icon('chevron_right', 'text-[16px]'); ?>
						<?php endif; ?>
						<span class="text-slate-900 font-semibold"><?php the_title(); ?></span>
					</nav>

					<h1 class="text-[1.85rem] sm:text-[2.2rem] md:text-[2.75rem] xl:text-[3.5rem] leading-[1.1] font-headline font-extrabold tracking-[-0.02em] text-slate-950 mb-6"><?php the_title(); ?></h1>

					<div class="flex items-center justify-between border-b border-slate-300/70 pb-8 flex-wrap gap-4">
						<div class="flex items-center gap-4">
							<?php echo get_avatar(get_the_author_meta('ID'), 48, '', '', ['class' => 'w-12 h-12 rounded-full object-cover']); ?>
							<div>
								<p class="font-headline font-bold text-slate-900 m-0"><?php echo esc_html(get_the_author()); ?></p>
								<p class="font-mono text-xs text-slate-600 m-0">
									<?php echo esc_html(get_the_date('M j, Y')); ?>
									<?php
									$word_count = str_word_count(wp_strip_all_tags(get_the_content()));
									$minutes    = max(1, (int) round($word_count / 200));
									?>
									• <?php printf(esc_html(_n('%d min read', '%d min read', $minutes, 'proger-blog')), $minutes); ?>
								</p>
							</div>
						</div>
					</div>
				</header>

				<?php if (has_post_thumbnail()) : ?>
					<figure class="mb-12 rounded-xl overflow-hidden border border-slate-200">
						<?php the_post_thumbnail('proger-hero', ['class' => 'w-full h-auto', 'fetchpriority' => 'high']); ?>
					</figure>
				<?php endif; ?>

				<div class="prose-matter">
					<?php the_content(); ?>
				</div>

				<footer class="mt-12 pt-8 border-t border-slate-300/70 flex flex-wrap items-center justify-between gap-4">
					<div class="flex flex-wrap gap-2">
						<?php $tags = get_the_tags(); if ($tags) : foreach ($tags as $tag) : ?>
							<a href="<?php echo esc_url(get_term_link($tag)); ?>" class="font-mono text-xs text-primary-container bg-white px-3 py-1 rounded-full hover:bg-slate-900 hover:text-primary transition-colors">
								#<?php echo esc_html($tag->slug); ?>
							</a>
						<?php endforeach; endif; ?>
					</div>
				</footer>
			</article>

			<?php if ($has_toc) : ?>
				<aside class="<?php echo esc_attr($aside_class); ?>" aria-label="<?php esc_attr_e('On this page', 'proger-blog'); ?>">
					<div class="single-toc-rail">
						<?php echo $toc_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</aside>
			<?php endif; ?>
		</main>

		<div class="single-floating-tools" aria-label="<?php esc_attr_e('Quick actions', 'proger-blog'); ?>">
				<?php if ($has_toc) : ?>
					<details class="single-floating-toc min-[1441px]:hidden">
						<summary class="single-fab single-fab--toc">
							<?php echo proger_blog_icon('menu_book', 'text-[20px]'); ?>
							<span class="sr-only"><?php echo esc_html($toc_title); ?></span>
						</summary>
						<button type="button" class="single-floating-toc__backdrop" data-toc-close aria-label="<?php esc_attr_e('Закрити зміст', 'proger-blog'); ?>"></button>
						<div class="single-floating-toc__panel">
						<div class="single-floating-toc__panel-header">
							<p class="single-floating-toc__panel-title"><?php echo esc_html($toc_title); ?></p>
							<button type="button" class="single-floating-toc__close" data-toc-close aria-label="<?php esc_attr_e('Закрити зміст', 'proger-blog'); ?>">
								<?php echo proger_blog_icon('close', 'text-[18px]'); ?>
							</button>
						</div>
						<?php echo $toc_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</details>
			<?php endif; ?>

			<a class="single-fab single-fab--up" href="#page-top" aria-label="<?php esc_attr_e('Вгору', 'proger-blog'); ?>">
				<?php echo proger_blog_icon('arrow_upward', 'text-[20px]'); ?>
				<span class="sr-only"><?php esc_html_e('Вгору', 'proger-blog'); ?></span>
			</a>
		</div>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
