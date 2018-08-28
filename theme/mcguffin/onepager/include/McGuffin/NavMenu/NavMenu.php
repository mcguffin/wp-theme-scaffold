<?php



namespace McGuffin\NavMenu;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use McGuffin\Core;


class NavMenu extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		// add menu item filter
	}

	/**
	 *	@action after_setup_theme
	 */
	public function setup() {
		// gather menus from acf
		// array slug => name
		while ( have_rows( 'header-top-sections', 'layout') ) {

		}
		while ( have_rows( 'header-bottom-sections', 'layout') ) {

		}
		while ( have_rows( 'footer-top-sections', 'layout') ) {

		}
		while ( have_rows( 'footer-bottom-sections', 'layout') ) {

		}
		// register_nav_menus( $menus );
	}



}
