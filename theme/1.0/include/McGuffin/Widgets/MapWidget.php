<?php 

namespace McGuffin\Widgets;

use McGuffin;

class MapWidget extends Widget {

	private $tag_opts = null;

	private $class_opts = null;

	private static $did_map_script = [];

	/**
	 *	Widget constructor
	 */
	function __construct() {
		parent::__construct( 
			'mcguffin_map_widget',
			__( 'Map', 'mcguffin'), 
			array( 'description' => __( 'A Google Map', 'mcguffin'), ),
			array(
				'map_id'	=> array(
					'type'	=> 'text',
					'name'	=> __('Map ID', 'mcguffin'),
					'width'	=> '50%',
					'default'	=> 'map',
				),
				'position_lat'	=> array(
					'type'	=> 'text',
					'name'	=> __('Map Center: Latitude', 'mcguffin'),
					'width'	=> '50%',
				),
				'position_lon'	=> array(
					'type'	=> 'text',
					'name'	=> __('Map Center: Longitude', 'mcguffin'),
					'width'	=> '50%',
				),
				'zoom'	=> array(
					'type'	=> 'number',
					'name'	=> __('Map Zoom', 'mcguffin'),
					'width'	=> '50%',
					'default'	=> 17,
				),
				'api_key'	=> array(
					'type'	=> 'text',
					'name'	=> __('GoogleMaps API-Key', 'mcguffin'),
				),
			)
		);
	}

	/**
	 *	Render Widget
	 *
	 *	@param	assoc	$args
	 *	@param	assoc	$instance
	 */
	function widget( $args, $instance ) {

		$instance	= wp_parse_args( $instance, $this->defaults );
		$map_id		= sanitize_key( $instance['map_id'] );
		$lat		= floatval( $instance['position_lat'] );
		$lng		= floatval( $instance['position_lon'] );
		$zoom		= intval( $instance['zoom'] ) || 13;
//		$api_key	= $instance['api_key'];
		$api_key	= get_option('{{theme_slug}}_google_maps_api_key');

		if ( ! $api_key || $ $lng || ! $lat ) {
			return;
		}

		echo $args['before_widget'];

		wp_enqueue_script( 'mcguffin-map' );

		?><div class="google-map-wrap">
			<div id="<?php echo $map_id; ?>"
				data-map="true"
				data-map-zoom="<?php echo $zoom; ?>"
				data-map-lat="<?php echo $lat; ?>"
				data-map-lng="<?php echo $lng; ?>"
			></div>
		</div>
		<?php

		if ( ! isset( self::$did_map_script[ $api_key ] ) ) {
			?><script src="https://maps.googleapis.com/maps/api/js?v=3&key=<?php echo $api_key ?>"></script><?php
			self::$did_map_script[ $api_key ] = true;
		}

		echo $args['after_widget'];
	}
}

