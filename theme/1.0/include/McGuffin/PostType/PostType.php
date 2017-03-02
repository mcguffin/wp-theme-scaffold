<?php

namespace McGuffin\PostType;

use McGuffin\Core;

abstract class PostType extends Core\Singleton {


	/**
	 * Private constructor
	 */
	protected function __construct() {
		add_action( 'init' , array( &$this , 'init' ) );
		add_action( 'init' , array( &$this , 'register_post_types' ) , 0 );
		parent::__construct();
	}

	/**
	 * Init hook.
	 * 
	 * @action init
	 */
	public function init() {
	}


	abstract function register_post_types();


	/**
	 *	Fired on plugin activation
	 */
	public static function activate() {
		// register post types, taxonomies
		self::instance()->register_post_types();
	
		// flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 *	Fired on plugin deactivation
	 */
	public static function deactivate() {
		// flush rewrite rules
		flush_rewrite_rules();
	}
	/**
	 *
	 */
	public static function uninstall() {
	}
}