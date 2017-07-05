<?php 

namespace McGuffin\Widgets;

use McGuffin;

class MapMarkerWidget extends Widget {
	private $tag_opts = null;
	
	private static $_instance_count = 0;

	private $class_opts = null;

	/**
	 *	Widget constructor
	 */
	function __construct() {
		parent::__construct( 
			'mcguffin_map_marker_widget',
			__( 'Map Marker', 'mcguffin'), 
			array( 'description' => __( 'A Google Map', 'mcguffin'), ),
			array(
				'title'	=> array(
					'type'	=> 'text',
					'name'	=> __('Title', 'mcguffin'),
				),
				'map_id'	=> array(
					'type'	=> 'text',
					'name'	=> __('Map ID', 'mcguffin'),
					'width'	=> '50%',
					'default'	=> 'map',
				),
				'street_address'	=> array(
					'type'	=> 'text',
					'name'	=> __('Street Address', 'mcguffin'),
					'width'	=> '50%',
				),
				'zip_city'	=> array(
					'type'	=> 'text',
					'name'	=> __('Zip and City', 'mcguffin'),
					'width'	=> '50%',
				),
				'phone'	=> array(
					'type'	=> 'text',
					'name'	=> __('Phone', 'mcguffin'),
					'width'	=> '50%',
				),
				'fax'	=> array(
					'type'	=> 'text',
					'name'	=> __('Fax', 'mcguffin'),
					'width'	=> '50%',
				),
				'email'	=> array(
					'type'	=> 'text',
					'name'	=> __('Email', 'mcguffin'),
					'width'	=> '50%',
				),

				'position_lat'	=> array(
					'type'	=> 'text',
					'name'	=> __('Latitude', 'mcguffin'),
					'width'	=> '50%',
				),
				'position_lon'	=> array(
					'type'	=> 'text',
					'name'	=> __('Longitude', 'mcguffin'),
					'width'	=> '50%',
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

		self::$_instance_count++;

		$instance 	= wp_parse_args( $instance, $this->defaults );
		$map_id		= sanitize_key( $instance['map_id'] );
		$lat		= floatval( $instance['position_lat'] );
		$lng		= floatval( $instance['position_lon'] );
		$flyout_id	= sprintf( '%s-%s',  $map_id, self::$_instance_count );

		?>
	    <div class="address-content flyout-for-<?php echo $map_id ?>" 
	    	data-map-marker="true"
	    	data-map-lat="<?php echo $lat; ?>"
	    	data-map-lng="<?php echo $lng; ?>"
	    	data-map-id="<?php echo $map_id ?>" id="<?php echo $flyout_id ?>">

			<div itemscope itemtype="http://schema.org/LocalBusiness">

				<?php

				if ( ! empty( $instance[ 'title' ] ) ) {
					$company = sanitize_text_field( $instance[ 'title' ] );
					?><h4 class="company-name">
						<span class="icon-logo-full"></span>
						<span itemprop="name"><?php echo $company ?></span>
					</h4><?php 
				}

				if ( ! empty( $instance[ 'street_address' ] ) || ! empty( $instance[ 'zip_city' ] ) ) {
					?><p class="icon-pin" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><?php

						if ( ! empty( $instance[ 'street_address' ] ) ) {
							$addr = sanitize_text_field( $instance[ 'street_address' ] );
							?><span itemprop="streetAddress">
								<?php echo $addr ?>
							</span><br /><?php 
						}

						if ( ! empty( $instance[ 'zip_city' ] ) ) {
							$city = sanitize_text_field( $instance[ 'zip_city' ] );
							?><span itemprop="addressLocality">
								<?php echo $city ?>
							</span><br /><?php 
						}

					?></p><?php
				}
			

				if ( ! empty( $instance[ 'phone' ] ) || ! empty( $instance[ 'fax' ] ) ) {
					$phone = sanitize_text_field( $instance[ 'phone' ] );
					$fax = sanitize_text_field( $instance[ 'fax' ] );
					?><p class="company-phone icon-phone">
						<?php
							if ( $phone ) {
							?>
								<span itemprop="telephone"><?php _e( 'Tel.:', 'fliesenzentrale' ) ?> <?php echo $phone ?></span>
							<?php
							}
							if ( $fax ) {
							?>
								<span itemprop="faxNumber"><?php _e( 'Fax:', 'fliesenzentrale' ) ?> <?php echo $fax ?></span>
							<?php
							}
						?>
					</p><?php 
				}

				if ( ! empty( $instance[ 'email' ] ) ) {
					$email = sanitize_email( $instance[ 'email' ] );

					?><p class="icon-mail">
						<a href="mailto:<?php echo $email ?>" itemprop="email"><?php echo $email ?></a>
					</p><?php 

				}

				?>
			</div>

		</div>
		<?php

	}
}

