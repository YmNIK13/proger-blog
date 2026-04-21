<?php
/**
 * Site footer.
 *
 * @package ProgerBlog
 */
?>

<footer class="bg-surface-container w-full py-12 px-8 mt-20 border-t border-white/5 relative z-10" style="z-index: 40;" role="contentinfo">
	<div class="max-w-screen-2xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
		<div class="text-white font-bold font-mono text-xs uppercase tracking-widest">
			© <?php echo esc_html(date_i18n('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>.
		</div>
		<div class="flex gap-6 flex-wrap justify-center">
			<?php
			$privacy_url = get_privacy_policy_url();
			if ($privacy_url) : ?>
				<a class="text-on-surface-variant hover:text-white font-mono text-xs uppercase tracking-widest hover:underline opacity-80 hover:opacity-100 transition-opacity" href="<?php echo esc_url($privacy_url); ?>">
					<?php esc_html_e('Privacy', 'proger-blog'); ?>
				</a>
			<?php endif; ?>
			<a class="text-on-surface-variant hover:text-white font-mono text-xs uppercase tracking-widest hover:underline opacity-80 hover:opacity-100 transition-opacity" href="<?php echo esc_url(home_url('/feed/')); ?>">
				<?php esc_html_e('RSS', 'proger-blog'); ?>
			</a>
			<?php
			$about = get_page_by_path('about');
			if ($about) : ?>
				<a class="text-primary font-mono text-xs uppercase tracking-widest hover:underline opacity-80 hover:opacity-100 transition-opacity" href="<?php echo esc_url(get_permalink($about)); ?>">
					<?php esc_html_e('About', 'proger-blog'); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
