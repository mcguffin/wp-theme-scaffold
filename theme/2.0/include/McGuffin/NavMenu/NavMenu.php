<?php


namespace McGuffin\NavMenu;

use McGuffin\Core;

class NavMenu extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'after_setup_theme', array( $this, 'setup' ) );

		add_filter('nav_menu_link_attributes', array($this,'link_attributes'), 10, 4 );
		add_filter('nav_menu_css_class', array($this,'item_css_class'), 10, 5);

	}

	/**
	 *	@action after_setup_theme
	 */
	public function setup() {
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'main'		=> esc_html__( 'Main Menu', 'mcguffin' ),
			'footer'	=> esc_html__( 'Footer Menu', 'mcguffin' ),
		) );

	}


	/**
	 *	@filter nav_menu_css_class
	 */
	public function item_css_class( $classes, $item, $args, $depth ) {

		if ( in_array( $args->theme_location, array( 'main', 'footer' ) ) ) {
			$classes[] = 'nav-item';
		}
		return $classes;
	}

	/**
	*	@filter nav_menu_link_attributes
	 */
	public function link_attributes( $atts, $item, $args, $depth) {
		if ( in_array( $args->theme_location, array( 'main', 'footer' ) ) ) {
			if ( !isset($atts['class']) ) {
				$atts['class'] = 'nav-link';
			} else {
				$atts['class'] .= ' nav-link';
			}

			if ( $class = get_field( 'item_link_css_class', $item ) ) {
				$classes = explode( ' ', $class );
				$classes = array_map( 'sanitize_html_class', $classes );
				$atts['class'] .= ' ' . implode( ' ', $classes );
			} else {
				$atts['class'] .= ' unstyled';
			}
		}
		return $atts;
	}
}
