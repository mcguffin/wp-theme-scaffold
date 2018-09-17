<?php

namespace McGuffin\ACF;


use McGuffin\Core;

if ( ! defined('ABSPATH') )
	die();


class ACF extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		if ( ! function_exists('acf') ) {
			return;
		}

		acf_add_options_sub_page( array(
			'page_title' 	=> __('___theme_name___ Configration','mcguffin'),
			'menu_title' 	=> __('Configuration','mcguffin'),
			'parent_slug' 	=> 'themes.php',
			'post_id' 		=> '___theme_slug____options',
			'autoload'		=> true,
			'capability'	=> 'manage_options',
			'post_id'		=> 'general',
		));

		acf_register_form(array(
			'id' => 'acf-form',
			'post_id' => 'new_post',
			'new_post' => false,
		));

		if ( function_exists( 'acf_add_customizer_section' ) ) {

			$panel_id = acf_add_customizer_panel( __( 'Site-Footer', 'mcguffin' ) );

			acf_add_customizer_section( array(

				'priority'				=> 50,
				'panel'					=> $panel_id,
				'capability'			=> 'edit_pages',
				'title'					=> __('Website Footer','mcguffin'),
				'description'			=> '',
				'description_hidden'	=> false,
				'storage_type'			=> 'option',
				'post_id' 				=> '___theme_slug____options',
			) );

		}


		add_filter( 'tiny_mce_before_init', array($this,'editor_settings'), 10, 2 );

		add_filter( 'acf/fields/google_map/api', array( $this, 'google_maps_api_key' ) );

	}

	public function editor_settings( $settings, $editor_id ) {
		if ( $editor_id === 'acf_content' ) {
		//	$settings['editor_height']	= '50';
			$settings['resize'] = 'vertical';
			$settings['wp_autoresize_on'] = true;
		}
		return $settings;
	}
	/**
	 *	@filter acf/fields/google_map/api
	 */
	public function google_maps_api_key( $api ) {
		$api['key'] = get_option( 'mcguffin_google_maps_api_key' );
		return $api;
	}

}
