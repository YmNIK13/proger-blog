<?php
/**
 * 404.
 *
 * @package ProgerBlog
 */

get_header(); ?>

<div class="flex flex-1 max-w-screen-2xl mx-auto w-full">
	<main id="main" class="flex-1 w-full px-6 md:px-12 py-24 min-h-screen flex flex-col items-center justify-center text-center">
		<p class="font-mono text-xs text-primary uppercase tracking-widest mb-4"><?php esc_html_e('Error 404', 'proger-blog'); ?></p>
		<h1 class="text-7xl md:text-9xl font-black font-headline text-white tracking-tight mb-6"><?php esc_html_e('Void.', 'proger-blog'); ?></h1>
		<p class="text-lg text-on-surface-variant max-w-lg mb-10"><?php esc_html_e('Сторінку не знайдено. Можливо, ви забули крапку з комою?', 'proger-blog'); ?></p>
		<div class="flex gap-4 flex-wrap justify-center">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="bg-primary text-on-primary font-bold px-6 py-3 rounded-md hover:bg-primary-fixed-dim hover:scale-105 transition-all duration-fast ease-standard inline-flex items-center gap-2">
				<?php echo proger_blog_icon('home', 'text-[20px]'); ?>
				<?php esc_html_e('На головну', 'proger-blog'); ?>
			</a>
			<a href="<?php echo esc_url(home_url('/?s=')); ?>" class="bg-transparent text-on-surface-variant font-bold px-6 py-3 rounded-md border border-outline-variant hover:bg-surface-bright transition-all duration-fast ease-standard inline-flex items-center gap-2">
				<?php echo proger_blog_icon('search', 'text-[20px]'); ?>
				<?php esc_html_e('Пошук', 'proger-blog'); ?>
			</a>
		</div>
	</main>
</div>

<?php get_footer(); ?>
