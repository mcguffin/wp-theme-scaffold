<?php



namespace McGuffin;


if ( ! defined('ABSPATH') ) {
	die('FU!');
}


/**
 * Base Class
 */
class Theme extends Core\Singleton {

	protected function __construct() {

		if ( function_exists('acf') && version_compare( acf()->version, '5.6', '>=' ) ) {
			ACF\ACF::instance();
		}

		Media\Image::instance();
		Media\Slider::instance();
		Media\SVG::instance();

		NavMenu\NavMenu::instance();

		Widgets\Widgets::instance();


		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'kses_allowed_protocols', array( $this, 'add_whatsapp_protocol' ) );

	}

	public function add_whatsapp_protocol( $protocols ) {
		$protocols[] = 'whatsapp';
		return $protocols;
	}

	public function setup() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
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

	}


	public function add_theme_supports() {

	}

	public function enqueue_scripts() {

	}

	public function getAssetUrl( $url ) {
		return get_template_directory_uri() . $url;
	}

	public function getAssetPath( $url ) {
		return get_template_directory() . $url;
	}

}
