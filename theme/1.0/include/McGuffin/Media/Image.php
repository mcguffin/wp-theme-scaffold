<?php


namespace McGuffin\Media;

use McGuffin\Core;

class Image extends Core\Singleton {
	
	
	protected function __construct() {
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		add_filter( 'image_size_names_choose', array( $this, 'image_size_names_choose' ) );
	}

	private function get_image_sizes() {
		return array(
			'din-portrait-large'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 900,
				'height'		=> 1272,
				'crop'			=> true,
				'selectable'	=> true,
			), 
			'din-portrait-medium'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 600,
				'height'		=> 848,
				'crop'			=> true,
				'selectable'	=> true,
			),
			'din-portrait-small'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 480,
				'height'		=> 679,
				'crop'			=> true,
				'selectable'	=> true,
			), 

			'din-landscape-large'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 1200,
				'height'		=> 848,
				'crop'			=> true,
				'selectable'	=> true,
			),
			'din-landscape-medium'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 900,
				'height'		=> 636,
				'crop'			=> true,
				'selectable'	=> true,
			),
			'din-landscape-small'	=> array(
				'name'			=> __('DIN portrait large','mcguffin'),
				'width'			=> 600,
				'height'		=> 424,
				'crop'			=> true,
				'selectable'	=> true,
			),

			'tv-1080p'	=> array(
				'name'			=> __('TV 16:9','mcguffin'),
				'width'			=> 1920,
				'height'		=> 1080,
				'crop'			=> true,
				'selectable'	=> true,
			),
			'tv-720p'	=> array(
				'name'			=> false,
				'width'			=> 1280,
				'height'		=> 720,
				'crop'			=> true,
				'selectable'	=> false,
			),
			'tv-480p'	=> array(
				'name'			=> false,
				'width'			=> 854,
				'height'		=> 480,
				'crop'			=> true,
				'selectable'	=> false,
			),

			'cinema-xl'	=> array(
				'name'			=> __('Cinema','mcguffin'),
				'width'			=> 1920,
				'height'		=> 817,
				'crop'			=> true,
				'selectable'	=> true,
			),
			'cinema-lg'	=> array(
				'name'			=> false,
				'width'			=> 1280,
				'height'		=> 545,
				'crop'			=> true,
				'selectable'	=> false,
			),
			'cinema-md'	=> array(
				'name'			=> false,
				'width'			=> 1024,
				'height'		=> 436,
				'crop'			=> true,
				'selectable'	=> false,
			),
			'cinema-sm'	=> array(
				'name'			=> false,
				'width'			=> 800,
				'height'		=> 340,
				'crop'			=> true,
				'selectable'	=> false,
			),

		);

	}

	public function get_image_size( $size_slug ) {
		global $_wp_additional_image_sizes;
		if ( in_array( $size_slug, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			return array(
				'width'		=> get_option( "{$size_slug}_size_w" ),
				'height'	=> get_option( "{$size_slug}_size_h" ),
				'crop'		=> get_option( "{$size_slug}_crop" ),
			);
		}
		if ( isset( $_wp_additional_image_sizes[$size_slug] ) ) {
			return $_wp_additional_image_sizes[$size_slug];
		}
		return false;
	}

	function add_image_sizes() {
		foreach ( $this->get_image_sizes() as $slug => $size ) {
			/* @vars $name, $width, $height, $crop, $selectable */
			extract( $size );
			add_image_size( $slug, $width, $height, $crop );
		}

	}

	function image_size_names_choose( $names ) {
		foreach ( $this->get_image_sizes() as $slug => $size ) {
			/* @vars $name, $width, $height, $crop, $selectable */
			extract( $size );
			if ( $selectable ) {
				$names[ $slug ] = $name;
			}
		}
		return $names;
	}





}

