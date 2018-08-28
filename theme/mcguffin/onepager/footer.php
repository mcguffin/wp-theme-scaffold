<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _s
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer text-center">
		<div class="site-info container">
			<?php
				dynamic_sidebar( 'footer' );
			?>
		</div><!-- .site-info -->
		<div class="container">
			<div class="pull-left-sm">
				<?php echo get_option('polyplanet_footer_notice'); ?>
			</div>
			<nav class="footer-menu pull-right-sm"><?php
					wp_nav_menu( array(
						'theme_location'	=>'footer',
						'menu_class'		=> 'menu horizontal legal-menu',
					));
			?></nav>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
