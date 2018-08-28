<?php

namespace McGuffin\Admin;

use McGuffin\Core;

if ( ! defined('ABSPATH') )
	die();


class Settings extends Core\Singleton {

	/**
	 * Setup which to WP options page the Rainbow options will be added.
	 *
	 * Possible values: general | writing | reading | discussion | media | permalink
	 */
	private $optionset = 'reading'; // writing | reading | discussion | media | permalink


	/**
	 * Private constructor
	 */
	protected function __construct() {
		add_action( 'admin_init' , array( &$this , 'register_settings' ) );

		// add default options
		add_option( '___theme_slug____404_page', 0 );
		add_option( '___theme_slug____google_maps_api_key', '' );

		acf_add_options_sub_page(array(
			'page_title' 	=> __('Company','mcguffin'),
			'menu_title' 	=> __('Company','mcguffin'),
			'parent_slug' 	=> 'options-general.php',
			'post_id'		=> 'company',
			'autoload'		=> true,
			'capability'	=> 'editor',
		));
	}

	/**
	 *	Setup options page.
	 *
	 *	@action admin_init
	 *	@uses settings_description
	 *	@uses checkbox
	 */
	function register_settings() {
		$settings_section = '___theme_slug____settings';
		// more settings go here ...

		add_settings_section( $settings_section, __( '___theme_name___',  'mcguffin' ), array( &$this, 'settings_description' ), $this->optionset );

		// ... and here
		$option_name = '___theme_slug____404_page';
		register_setting( $this->optionset , $option_name, 'intval' );

		add_settings_field(
			$option_name,
			__( 'Not Found Page',  'mcguffin' ),
			array( $this, 'select_page' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name' => $option_name,
			)
		);


		// ... and here
		$option_name = '___theme_slug____google_maps_api_key';
		register_setting( $this->optionset , $option_name, 'sanitize_text_field' );

		add_settings_field(
			$option_name,
			__( 'Google Maps API-Key',  'mcguffin' ),
			array( $this, 'input_text' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name' => $option_name,
			)
		);



	}

	/**
	 *	Print some documentation for the optionset
	 *
	 *	@usedby register_settings
	 */
	public function settings_description() {
	}

	/**
	 *	Output checkbox
	 *
	 *	@usedby register_settings
	 */
	public function input_text( $args ){
		$setting = get_option( $args['option_name'] );

		?><input class="regular-text ltr" name="<?php echo $args['option_name'] ?>" value="<?php echo $setting ?>" /><?php

		if ( isset($args['option_description']) ) {
			?><p class="description"><?php
				echo $args['option_description']
			?></p><?php
		}
	}
	/**
	 *	Output checkbox
	 *
	 *	@usedby register_settings
	 */
	public function select_page( $args ){
		$setting = get_option( $args['option_name'] );


		?><label for="<?php echo $args['option_name']; ?>"><?php
			echo wp_dropdown_pages( array(
				'name' => $args['option_name'],
				'echo' => 0,
				'show_option_none' => __( '&mdash; Select &mdash;' ),
				'option_none_value' => '0',
				'selected' => $setting
			) );
		?></label><?php

		if ( isset($args['option_description']) ) {
			?><p class="description"><?php
				echo $args['option_description']
			?></p><?php
		}
	}

}
