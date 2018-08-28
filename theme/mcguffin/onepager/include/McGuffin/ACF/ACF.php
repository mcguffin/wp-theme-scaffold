<?php

namespace McGuffin\ACF;

use McGuffin\Core;

class ACF extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// add theme options page
		acf_add_options_sub_page(array(
			'page_title'	=> __( 'Theme Setting', 'mcguffin' ),
			'menu_title'	=> __( 'Settings', 'mcguffin' ),
			'parent_slug'	=> 'themes.php',
			'capability'	=> 'manage_options',
			'post_id'		=> 'general',
			'autoload'		=> true,
			'update_button'	=> __( 'Save', 'mcguffin' ),
		));

		acf_add_options_sub_page(array(
			'page_title'	=> __( 'Header and Footer', 'mcguffin' ),
			'menu_title'	=> __( 'Header / Footer', 'mcguffin' ),
			'parent_slug'	=> 'themes.php',
			'capability'	=> 'edit_pages',
			'post_id'		=> 'layout',
			'autoload'		=> true,
			'update_button'	=> __( 'Apply Changes', 'mcguffin' ),
		));

		if ( function_exists('acf_add_customizer_section') ) {
			$panel_header = acf_add_customizer_panel( __( 'Header', 'mcguffin' ) );

			acf_add_customizer_section( array(
				'title'			=> __( 'Above', 'mcguffin' ),
				'capability'	=> 'edit_pages',
				'storage_type'	=> 'theme_mod',
				'post_id'		=> 'header-top',
				'panel'			=> $panel_header,
			));

			acf_add_customizer_section( array(
				'title'			=> __( 'Bottom', 'mcguffin' ),
				'capability'	=> 'edit_pages',
				'storage_type'	=> 'theme_mod',
				'post_id'		=> 'header-bottom',
				'panel'			=> $panel_header,
			));

			$panel_footer = acf_add_customizer_panel( __( 'Header', 'mcguffin' ) );

			acf_add_customizer_section( array(
				'title'			=> __( 'Footer', 'mcguffin' ),
				'capability'	=> 'edit_pages',
				'storage_type'	=> 'theme_mod',
				'post_id'		=> 'footer-top',
				'panel'			=> $panel_footer,
			));

			acf_add_customizer_section( array(
				'title'			=> __( 'Below Footer', 'mcguffin' ),
				'capability'	=> 'edit_pages',
				'storage_type'	=> 'theme_mod',
				'post_id'		=> 'footer-bottom',
				'panel'			=> $panel_footer,
			));

		}

	}
}
