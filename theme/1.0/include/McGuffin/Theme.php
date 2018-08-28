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

		Admin\Settings::instance();
		Admin\Customizer::instance();
		Media\Embed::instance();
		Media\Image::instance();
		Media\SVG::instance();
		Media\Upload::instance();
//		WPGridbuilder\Gridbuilder::instance();
		PostType\PostTypes::instance();
		Widgets\Widgets::instance();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'kses_allowed_protocols', array( $this, 'add_whatsapp_protocol' ) );

		add_filter('acf/fields/google_map/api', array( $this, 'google_maps_api_key' ) );

	}

	public function admin_init() {
		Admin\TinyMce\BigSmall\BigSmall::instance();
		Admin\TinyMce\Bootstrap\Bootstrap::instance();
		Admin\TinyMce\Clear\Clear::instance();
		Admin\TinyMce\Mobile\Mobile::instance();
		Admin\TinyMce\Shy\Shy::instance();
		Admin\TinyMce\Uppercase\Uppercase::instance();
	}

	public function google_maps_api_key( $api = null ) {
		$api['key'] = get_option('___theme_slug____google_maps_api_key');
		return $api;
	}



	public function add_whatsapp_protocol( $protocols ) {
		$protocols[] = 'whatsapp';
		return $protocols;
	}

	public function setup() {

		load_theme_textdomain( 'mcguffin', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Editor Style
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_editor_style();

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'social'	=> esc_html__( 'Social Menu', 'mcguffin' ),
			'contact'	=> esc_html__( 'Contact Menu (Mobile)', 'mcguffin' ),
			'footer'	=> esc_html__( 'Footer Menu', 'mcguffin' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		) );
	}


	function enqueue_scripts() {

		$version	= wp_get_theme()->Version;
		$bs_version	= '3.3.7';

		wp_enqueue_style( '___theme_slug_dash___-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,300,400italic,700,700italic', array() );
		wp_enqueue_style( '___theme_slug_dash___-style', get_stylesheet_uri(), array( '___theme_slug_dash___-fonts' ), $version );

		wp_register_script( 'modernizr',
			$this->getAssetUrl( '/js/modernizr.custom.js' ),
			array(),
			$version,
			true
		);
		wp_register_script( 'bootstrap',
			$this->getAssetUrl( '/js/bootstrap/bootstrap.js' ),
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
			$this->getAssetUrl( '/js/jquery.mobile.custom.js' ),
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
		wp_register_script( 'google-maps-js-api',
			'https://maps.googleapis.com/maps/api/js?v=3&key='. get_option('___theme_slug____google_maps_api_key'),
			array( 'jquery' ),
			$version,
			true
		);

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

		wp_enqueue_script( '___theme_slug_dash___', 	$this->getAssetUrl( '/js/project.js' ), $deps, $version, true );
	}

	public function getAssetUrl( $url ) {
		return trailingslashit( get_template_directory_uri() ) . $url;
	}

	public function getAssetPath( $url ) {
		return trailingslashit( get_template_directory() ) . $url;
	}

}
