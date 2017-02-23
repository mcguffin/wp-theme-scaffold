<?php

namespace McGuffin\Admin\TinyMce;


use McGuffin\Core;


abstract class TinyMce extends Core\Singleton {

	/**
	 *	Module name
	 *	lowercase string.
	 *	You *must* override this in a derived class
	 */
	protected $module_name = null;

	/**
	 *	Override to add buttons
	 *
	 *	Usage:
	 *	protected $editor_buttons = array(
	 *		'mce_buttons'	=> array(
	 *			'append_button'	=> false,
	 *			'insert_button_at_position'	=> 3,
	 *		),
	 *		'mce_buttons_2'	=> array(
	 *			'append_button_to_second_row'	=> false,
	 *		),
	 *	);
	 */
	protected $editor_buttons = array();

	/**
	 *	Plugin params
	 *	An arbitrary array which will be made avaialable in JS
	 *	under the varname mce_{$module_name}.
	 *
	 */
	protected $plugin_params = false;
	
	/**
	 *	Load custom css for toolbar.
	 *	boolean
	 */
	protected $toolbar_css = false;

	/**
	 *	Load custom css for editor.
	 *	boolean
	 */
	protected $editor_css = false;

	/**
	 *	Asset dir for derived class
	 *	string path
	 */
	private $asset_dir_uri = null;


	/**
	 * Private constructor
	 */
	protected function __construct() {

		if ( is_null( $this->module_name ) ) {
			throw( new Exception( '`$module_name` must be defined in a derived classes.' ) );
		}

		$parts = array_slice( explode( '\\', get_class( $this ) ), 0, -1 );
		array_unshift( $parts, 'include' );

		$this->asset_dir_uri = trailingslashit( get_template_directory_uri() ) . trailingslashit( implode( DIRECTORY_SEPARATOR, $parts ) );

		// add tinymce buttons
		$this->editor_buttons = wp_parse_args( $this->editor_buttons, array(
			'mce_buttons'	=> false,
			'mce_buttons_2'	=> false,
		) );
		
		foreach ( $this->editor_buttons as $hook => $buttons ) {
			if ( $buttons !== false ) {
				add_filter( $hook, array( $this, 'add_buttons' ) );
			}
		}
		
		// add tinymce plugin
		add_filter( 'mce_external_plugins', array( $this, 'add_plugin' ) );

		// add tinymce plugin parameters
		if ( $this->plugin_params !== false ) {
			add_action( 'wp_enqueue_editor' , array( $this , 'mce_localize' ) );
		}

		if ( $this->editor_css !== false ) {
			add_filter('mce_css' , array( $this , 'mce_css' ) );
		}
		if ( $this->toolbar_css !== false ) {
			add_action( "admin_print_scripts", array( $this, 'enqueue_toolbar_css') );
		}


	}


	/**
	 *	Add MCE plugin
	 *
	 *	@filter mce_external_plugins
	 */
	public function add_plugin( $plugins_array ) {
		$plugins_array[ $this->module_name ] = $this->get_asset_url( sprintf( 'js/plugin.js', $this->module_name ) );
		return $plugins_array;
	}

	/**
	 *	Add toolbar Buttons.
	 *
	 *	@filter mce_buttons, mce_buttons_2
	 */
	public function add_buttons( $buttons ) {
		$hook = current_filter();
		if ( isset( $this->editor_buttons[ $hook ] ) && is_array( $this->editor_buttons[ $hook ] ) ) {
			foreach ( $this->editor_buttons[ $hook ] as $button => $position ) {
				if ( $position === false ) {
					$buttons[] = $button;
				} else {
					array_splice( $buttons, $position, 0, $button );
				}
			}
		}
		return $buttons;
	}


	/**
	 *	Enqueue toolbar css
	 *
	 *	@action admin_print_scripts
	 */
	public function enqueue_toolbar_css() {
		$asset_id = sprintf( 'tinymce-%s-toolbar-css', $this->module_name );
		$asset_url = $this->get_asset_url( 'css/toolbar.css' );
		wp_enqueue_style( $asset_id, $asset_url );
	}

	/**
	 *	Add editor css
	 *
	 *	@filter mce_css
	 */
	public function mce_css( $styles ) {
		$mce_css = $this->get_asset_url( 'css/editor.css' );
		$styles .= ','. $mce_css;
		return $styles;
	}


	/**
	 *	print plugin settings
	 *
	 *	@action wp_enqueue_editor
	 */
	public function mce_localize( $to_load ) {
		if ( $to_load['tinymce'] ) {
			$varname = sprintf( 'mce_%s', $this->module_name );
			$params = json_encode($this->plugin_params );
			printf( '<script type="text/javascript"> var %s = %s;</script>', $varname, $params );
    	}
	}

	/**
	 *	Get asset url for this editor plugin
	 *
	 *	@param	string	$asset	URL part relative to plugin class
	 *	@return wp_enqueue_editor
	 */
	protected function get_asset_url( $asset ) {
		return $this->asset_dir_uri . ltrim( $asset, '/' );
	}

}
