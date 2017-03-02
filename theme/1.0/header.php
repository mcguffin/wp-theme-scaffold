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

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link sr-only" href="#content"><?php esc_html_e( 'Skip to content', 'mcguffin' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="container">
			<div class="site-branding navbar-header">
				<button type="button" class="navbar-toggle collapsed glyphicon glyphicon-menu-hamburger" 
						data-toggle="collapse" 
						data-target="#site-navigation" 
						aria-expanded="false">
					<span class="sr-only"><?php esc_html_e( 'Primary Menu', 'mcguffin' ); ?></span>
				</button>

				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="navbar-brand site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="navbar-brand site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
			</div><!-- .site-branding -->
			
			<nav id="site-navigation" class="main-navigation collapse navbar-collapse">
				<?php 
					

					wp_nav_menu( array( 
						'theme_location' => 'primary', 
						'menu_id' => 'primary-menu' , 
						'menu_class' => 'nav navbar-nav' ,
						'container_id' => '',
						'container' => 'div' ,
						'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					) ); ?>
			</nav><!-- #site-navigation -->
		</div>

	</header><!-- #masthead -->
	<div class="jumbotron">
		<div class="container">
			<h1>Well...</h1>
			<p>So far so good</p>
		</div>
	</div>
	<div id="content" class="site-content">
		<div class="container">
