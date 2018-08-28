<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package _s
 */

?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-spy="scroll" data-target=".anchor-nav-container">
<div id="page" class="hfeed site">
	<a class="skip-link sr-only" href="#content"><?php esc_html_e( 'Skip to content', 'polyplanet'); ?></a>

	<header id="masthead" class="site-header" data-spy="affix" data-offset-top="38">
		<div class="container">
			<div class="row">
				<div class="col-xs-5 col-sm-4 col-md-2 site-branding navbar-header">
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img class="logo-full" src="<?php echo McGuffin\Theme::instance()->getAssetUrl( '/img/logo.svg' ) ?>" />
						<span class="sr-only"><?php bloginfo( 'name' ); ?></span>
					</a></h1>
				</div><!-- .site-branding -->


				<nav class="main-navigation hidden-xs hidden-sm col-md-10" id="site-navigation">
					<div class="col-md-10 anchor-nav-container">
						<?php
							$grid_data = get_post_meta( get_the_ID(), '_grid_data', true );
						//	polyplanet_the_anchor_nav( $grid_data );
						?>
					</div>

					<div class="col-md-2">
						<?php

						wp_nav_menu(array(
							'theme_location'	=> 'social',
							'link_before'		=> '<span class="sr-only">',
							'link_after'		=> '</span>',
							'menu_class'		=> 'menu icons-menu social-menu',
							'container_class'	=> 'social-menu-container pull-right',
						));
						?>
					</div>
				</nav><!-- #site-navigation -->

				<div class="col-xs-6 col-sm-8 col-md-10 visible-xs visible-sm mobile-menubar">
					<div class="text-right">
						<?php

						wp_nav_menu(array(
							'theme_location'	=> 'contact',
							'link_before'		=> '<span class="sr-only">',
							'link_after'		=> '</span>',
							'menu_class'		=> 'menu horizontal icons-menu contact-menu',
							'container_class'	=> 'contact-menu-container',
						));
						?>

						<button aria-controls="mobile-navigation" class="btn-icon icon-menu"><span class="sr-only"><?php __('Menu', 'polyplanet'); ?></span></button>
					</div>
				</div>

			</div><!-- .row -->

		</div><!-- .container -->

		<nav id="mobile-navigation" class="mobile-navigation visible-xs visible-sm" aria-expanded="false">
			<!-- close btn -->

			<div class="mobile-nav-content">
				<?php

					?><div class="anchor-nav-wrap"><?php
						// anchor menu
					//	polyplanet_the_anchor_nav( $grid_data );
					?></div><?php

					// legals
					wp_nav_menu( array(
						'theme_location'	=>'footer',
						'menu_class'		=> 'menu horizontal legal-menu',
						'container_class'	=> 'legal-menu-container',
					));


					// social
					wp_nav_menu(array(
						'theme_location'	=> 'social',
						'link_before'		=> '<span class="sr-only">',
						'link_after'		=> '</span>',
						'menu_class'		=> 'menu icons-menu horizontal',
						'container_class'	=> 'social-menu-container',
					));

				?>
			</div>
		</nav>

	</header><!-- #masthead -->
	<div id="content" class="site-content">
