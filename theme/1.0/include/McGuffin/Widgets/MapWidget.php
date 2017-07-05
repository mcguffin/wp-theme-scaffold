<?php 

namespace McGuffin\Widgets;

use McGuffin;

class MapWidget extends Widget {

	private $tag_opts = null;

	private $class_opts = null;

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
					'type'		=> 'text',
					'name'		=> __('Map ID', 'mcguffin'),
					'default'	=> 'onepager-map',
				),
				'map_view'	=> array(
					'type'		=> 'maps',
					'name'		=> __('Map View', 'mcguffin'),
					'marker'	=> false,
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

		$lat		= floatval( $instance['map_view']['lat'] );
		$lng		= floatval( $instance['map_view']['lng'] );
		$zoom		= intval( $instance['map_view']['zoom'] );
		$api_key	= get_option('onepager_google_maps_api_key'); //$instance['api_key'];

		if ( ! $api_key || ! $lng || ! $lat ) {
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

		echo $args['after_widget'];
	}
}

