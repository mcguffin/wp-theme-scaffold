<?php
/**
 * _s functions and definitions.
 *
 * @link https://codex.wordpress.org/Functions_File_Explained
 *
 * @package _s
 */

namespace McGuffin;

/**
 * Base Class
 */
class Theme extends Core\Singleton {

	protected function __construct() {
		if ( is_admin() ) {
			Admin\Settings::instance();
		}

		Admin\Customizer::instance();

		Media\Embed::instance();
		Media\Image::instance();
		Media\SVG::instance();
		Media\Upload::instance();


		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'kses_allowed_protocols', array( $this, 'add_whatsapp_protocol' ) );

		add_filter( 'acf/fields/google_map/api', array( $this, 'google_maps_api_key' ) );

	}

	public function admin_init() {
	}

	public function google_maps_api_key( $api = null ) {
		$api['key'] = get_option('{{theme_slug}}_google_maps_api_key');
		return $api;
	}



	public function add_whatsapp_protocol( $protocols ) {
		$protocols[] = 'whatsapp';
		return $protocols;
	}

	public function setup() {

		load_theme_textdomain( 'mcguffin', get_template_directory() . '/languages' );

		//*
		add_theme_support( 'post-formats', array(
			'aside',
			'gallery',
			'link',
			'image',
			'quote',
			'status',
			'video',
			'audio',
			'chat',
		) );
		/*/
		remove_theme_support('post-formats');
		//*/

		//*
		add_theme_support( 'post-thumbnails', array(
			'post',
			'page',
		) );
		/*/
		remove_theme_support( 'post-thumbnails' );
		//*/

		//*
		add_theme_support( 'custom-background', array(
			'default-image'				=> '',
			'default-preset'			=> 'default',
			'default-position-x'		=> 'left',
			'default-position-y'		=> 'top',
			'default-size'				=> 'auto',
			'default-repeat'			=> 'repeat',
			'default-attachment'		=> 'scroll',
			'default-color'				=> '',
			'wp-head-callback'			=> '_custom_background_cb',
			'admin-head-callback'		=> '',
			'admin-preview-callback'	=> '',
		) );
		/*/
		remove_theme_support( 'custom-background' );
		//*/

		//*
		add_theme_support( 'custom-header', array(
			'default-image'				=> '',
			'random-default'			=> false,
			'width'						=> 0,
			'height'					=> 0,
			'flex-height'				=> false,
			'flex-width'				=> false,
			'default-text-color'		=> '',
			'header-text'				=> true,
			'uploads'					=> true,
			'wp-head-callback'			=> '',
			'admin-head-callback'		=> '',
			'admin-preview-callback'	=> '',
			'video'						=> false,
			'video-active-callback'		=> 'is_front_page',
		) );
		/*/
		remove_theme_support( 'custom-header' );
		//*/

		//*
		add_theme_support( 'custom-logo', array(
			'height'		=> 100,
			'width'			=> 400,
			'flex-height'	=> true,
			'flex-width'	=> true,
			'header-text'	=> array( 'site-title', 'site-description' ),
		) );
		/*/
		remove_theme_support( 'custom-logo' );
		//*/

		//*
		add_theme_support( 'automatic-feed-links' );
		/*/
		remove_theme_support( 'automatic-feed-links' );
		//*/


		//*
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		/*/
		remove_theme_support('html5');
		//*/




		//*
		add_theme_support( 'title-tag' );
		/*/
		remove_theme_support( 'title-tag' );
		//*/

		//*
		add_theme_support( 'customize-selective-refresh-widgets' );
		/*/
		remove_theme_support( 'customize-selective-refresh-widgets' );
		//*/


		/*
		 * Editor Style
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_editor_style();

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'main'		=> esc_html__( 'Main Menu', 'mcguffin' ),
			'footer'	=> esc_html__( 'Footer Menu', 'mcguffin' ),
		) );
	}


	function enqueue_scripts() {

		$version	= wp_get_theme()->Version;
		$bs_version	= '3.3.7';

		wp_enqueue_style( '{{theme_slug_dash}}-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,300,400italic,700,700italic', array() );
		wp_enqueue_style( '{{theme_slug_dash}}-style', get_stylesheet_uri(), array( '{{theme_slug_dash}}-fonts' ), $version );

		wp_register_script( 'modernizr',
			$this->getAssetUrl( '/js/vendor/modernizr-custom.js' ),
			array(),
			$version,
			true
		);
		wp_register_script( 'bootstrap',
			$this->getAssetUrl( '/js/vendor/bootstrap/bootstrap.js' ),
			array('jquery'),
			$bs_version,
			true
		);
		wp_register_script( 'aria-expanded',
			$this->getAssetUrl( '/js/aria-expanded.js' ),
			array('jquery'),
			$version,
			true
		);
		wp_register_script( 'jquery-mobile',
			$this->getAssetUrl( '/js/vendor/jquery.mobile.custom.js' ),
			array('jquery'),
			$version,
			true
		);
		wp_register_script( 'scroll-here',
			$this->getAssetUrl( '/js/scroll-here.js' ),
			array('jquery'),
			$version,
			true
		);
		wp_register_script( 'jquery-viewport-events',
			$this->getAssetUrl( '/js/jquery-viewport-events.js' ),
			array('jquery'),
			$version,
			true
		);
		wp_register_script( 'lazyload',
			$this->getAssetUrl( '/js/lazyload.js' ),
			array('jquery'),
			$version,
			true
		);
		wp_register_script( 'objectfit-polyfil',
			$this->getAssetUrl( '/js/objectfit.js' ),
			array( 'jquery' ),
			$version,
			true
		);

		if ( $gm_api_key = get_option('mcguffin_google_maps_api_key') ) {
			wp_register_script( 'google-maps-js-api',
				'https://maps.googleapis.com/maps/api/js?v=3&key='. $gm_api_key,
				array( 'jquery' ),
				$version,
				true
			);
		}

		$deps = array(
			'jquery',
			'modernizr',
			'bootstrap',
			'jquery-mobile',
			'jquery-viewport-events',
			'lazyload',
			'scroll-here',
			'aria-expanded',
			'objectfit-polyfil'
		);

		wp_enqueue_script( '{{theme_slug_dash}}', 	$this->getAssetUrl( '/js/project.js' ), $deps, $version, true );
	}

	public function getAssetUrl( $url ) {
		return trailingslashit( get_template_directory_uri() ) . $url;
	}

	public function getAssetPath( $url ) {
		return trailingslashit( get_template_directory() ) . $url;
	}

}
