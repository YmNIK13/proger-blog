<?php
/**
 * Single post — "white card on void" layout with right TOC.
 *
 * @package ProgerBlog
 */

get_header(); ?>

<div class="flex flex-1 max-w-screen-2xl mx-auto w-full">
	<?php get_sidebar(); ?>

	<?php while (have_posts()) : the_post(); ?>
		<main id="main" class="flex-1 lg:ml-64 px-4 sm:px-8 py-12 lg:py-20 flex flex-col xl:flex-row justify-center gap-12 relative min-h-screen">
			<article class="w-full max-w-[750px] bg-white rounded-xl text-slate-900 shadow-ambient p-6 sm:p-12 xl:p-16 relative">
				<header class="mb-12">
					<nav class="flex items-center gap-2 text-slate-500 font-mono text-xs uppercase tracking-wider mb-6 flex-wrap" aria-label="<?php esc_attr_e('Breadcrumbs', 'proger-blog'); ?>">
						<a class="hover:text-primary-container transition-colors" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'proger-blog'); ?></a>
						<?php echo proger_blog_icon('chevron_right', 'text-[16px]'); ?>
						<?php
						$cats = get_the_category();
						if ($cats) : $cat = $cats[0]; ?>
							<a class="hover:text-primary-container transition-colors" href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
							<?php echo proger_blog_icon('chevron_right', 'text-[16px]'); ?>
						<?php endif; ?>
						<span class="text-slate-800 font-semibold"><?php the_title(); ?></span>
					</nav>

					<h1 class="text-[2rem] md:text-[2.75rem] xl:text-[3.5rem] leading-[1.1] font-headline font-extrabold tracking-[-0.02em] text-slate-950 mb-6"><?php the_title(); ?></h1>

					<div class="flex items-center justify-between border-b border-slate-200/60 pb-8 flex-wrap gap-4">
						<div class="flex items-center gap-4">
							<?php echo get_avatar(get_the_author_meta('ID'), 48, '', '', ['class' => 'w-12 h-12 rounded-full object-cover']); ?>
							<div>
								<p class="font-headline font-bold text-slate-900 m-0"><?php echo esc_html(get_the_author()); ?></p>
								<p class="font-mono text-xs text-slate-500 m-0">
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
					<figure class="mb-12 rounded-xl overflow-hidden border border-slate-100">
						<?php the_post_thumbnail('proger-hero', ['class' => 'w-full h-auto', 'fetchpriority' => 'high']); ?>
					</figure>
				<?php endif; ?>

				<div class="prose-matter">
					<?php the_content(); ?>
				</div>

				<footer class="mt-12 pt-8 border-t border-slate-200/60 flex flex-wrap items-center justify-between gap-4">
					<div class="flex flex-wrap gap-2">
						<?php $tags = get_the_tags(); if ($tags) : foreach ($tags as $tag) : ?>
							<a href="<?php echo esc_url(get_term_link($tag)); ?>" class="font-mono text-xs text-primary-container bg-slate-100 px-3 py-1 rounded-full hover:bg-primary-container hover:text-on-primary-container transition-colors">
								#<?php echo esc_html($tag->slug); ?>
							</a>
						<?php endforeach; endif; ?>
					</div>
				</footer>
			</article>

			<?php if (get_theme_mod('proger_enable_toc', true)) : ?>
				<aside class="hidden xl:block w-64 shrink-0" aria-label="<?php esc_attr_e('On this page', 'proger-blog'); ?>">
					<div class="sticky top-24">
						<?php echo do_blocks('<!-- wp:proger-blog/toc /-->'); ?>
					</div>
				</aside>
			<?php endif; ?>
		</main>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
