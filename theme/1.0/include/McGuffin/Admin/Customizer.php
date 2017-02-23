<?php

namespace McGuffin\Admin;

use McGuffin\Core;

if ( ! defined('ABSPATH') ) 
	die();

class Customizer extends Core\Singleton {

	protected function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );	
		add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
	}

	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	function customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}


	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	function customize_preview_js() {

		$version	= wp_get_theme()->Version;

		wp_enqueue_script( '{{theme_slug}}_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), $version, true );

	}

}

