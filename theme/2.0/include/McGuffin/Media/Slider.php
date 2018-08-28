<?php


namespace McGuffin\Media;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use McGuffin\Admin;
use McGuffin\Core;

class Slider extends Admin\Singleton {

	private $slider_count = 0;
	private $prev_wp_query = null;

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'post_gallery', array( $this, 'post_gallery' ), 10, 3 );

		add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
		add_action( 'wp_enqueue_media', array( $this, 'wp_enqueue_media' ) );


		parent::__construct();
	}
	/**
	 *	@filter post_gallery
	 */
	public function post_gallery( $output, $atts, $instance ) {
		$get_posts_args = shortcode_atts( array(
			'include'			=> '',
			'post_status' 		=> 'inherit',
			'post_type' 		=> 'attachment',
			'post_mime_type'	=> 'image',
			'order'				=> 'ASC',
			'orderby'			=> 'post__in',
			'size'				=> 'content-large',
		), $atts );

		$attachments = get_posts( $get_posts_args );
		return $this->get_slider( $attachments, $atts );
	}

	/**
	 *	@param string $what
	 *	@return mixed
	 */
	public function __get( $what ) {
		if ( isset( $this->current_slider_args[ $what ] ) ) {
			return $this->current_slider_args[ $what ];
		}
	}

	/**
	 *	@param array $slider_items
	 *	@param array $slider_args
	 */
	public function get_slider( $slider_items, $slider_args = array() ) {
		global $wp_query;

		$this->slider_count++;

		$this->current_slider_args = wp_parse_args( $slider_args, array(
			'class'			=> '', // cinema, tv, din-landscape
			'interval'		=> 10,
			'autoplay'		=> false,
			'caption'		=> true,

			'id'			=> sprintf( 'slider-%d', $this->slider_count ),

			'item_tag'		=> 'figure',
			'item_class'	=> '',

			'size'			=> 'tv-1080p',
			'image_attr'	=> array(),
			'add_sources'	=> array(),
		));

		// normalize slider items to post IDs
		if ( ! is_array( $slider_items ) ) {
			$slider_items = explode( ',', $slider_items );
		}
		$slider_items = array_map( array( $this, 'normalize_slider_item' ), $slider_items );
		$slider_items = array_filter( $slider_items, 'absint' );

		$this->prev_wp_query = $wp_query;

		// no items - no slider!
		if ( ! count($slider_items) ) {
			return;
		}

		// query slider items
		query_posts( array(
			'posts_per_page'	=> -1,
			'post__in'			=> $slider_items,
			'orderby'			=> 'post__in',
			'post_status'		=> array( 'publish', 'inherit' ),
			'post_type'			=> 'any',
		) );

		ob_start( );

		if ( isset( $this->current_slider_args['image_attr']['sizes'] ) ) {
			add_filter( 'wp_calculate_image_sizes', array( $this, 'image_sizes_attribute' ), 10, 5 );
		}

		if ( ! empty( $this->current_slider_args['add_sources'] ) ) {
			add_filter( 'wp_calculate_image_srcset', array( $this, 'image_sources' ), 10, 5 );
		}

		get_template_part( 'template-parts/slider/slider', '' );

		if ( isset( $this->current_slider_args['add_sources'] ) ) {
			remove_filter( 'wp_calculate_image_srcset', array( $this, 'image_sources' ), 10 );
		}

		if ( isset( $this->current_slider_args['image_attr']['sizes'] ) ) {
			remove_filter( 'wp_calculate_image_sizes', array( $this, 'image_sizes_attribute' ), 10 );
		}


		$wp_query = $this->prev_wp_query;
		wp_reset_query();
		$ret = ob_get_clean( );

		return $ret;
	}

	/**
	 *	@filter wp_calculate_image_sizes
	 */
	public function image_sizes_attribute( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
		if ( isset( $this->current_slider_args['image_attr']['sizes'] ) ) {
			return $this->current_slider_args['image_attr']['sizes'];
		}
		return $sizes;
	}
	/**
	 *	@filter wp_calculate_image_srcset
	 */
	public function image_sources( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $this->current_slider_args['add_sources'] as $size => $descriptor ) {
			$source = wp_get_attachment_image_src( $attachment_id, $size );
			$sources[] = array(
				'url'			=> $source[0],
				'descriptor'	=> $descriptor,
				'value'			=> $descriptor == 'w' ? $source[1] : $source[2],
			);
		}
		return $sources;
	}

	/**
	 *	@param int|array|WP_Post $item
	 */
	private function normalize_slider_item( $item ) {
		if ( is_numeric( $item ) ) {
			return absint( $item );
		}
		if ( is_array( $item ) && ( ( $lowercaseID = isset( $item['id'] ) ) || isset( $item['ID'] ) ) ) {
			return $this->normalize_slider_item( $item[ $lowercaseID ? 'id' : 'ID' ] );
		}
		if ( is_object( $item ) && ( ( $lowercaseID = isset( $item->id ) ) || isset( $item->ID ) ) ) {
			$prop = $lowercaseID ? 'id' : 'ID';
			return $this->normalize_slider_item( $item->$prop );
		}
		return false;
	}

	/**
	 *	@action wp_enqueue_media
	 */
	public function wp_enqueue_media() {
		$asset_url = $this->getAssetUrl( 'js/slider.js' );

		wp_enqueue_script( 'mguffin-gallery-admin', $asset_url );
	}


	/**
	 *	Media Gallery UI Template
	 *
	 *	@action print_media_templates
	 */
	public function print_media_templates() {
		?>
		<script type="text/html" id="tmpl-mcguffin-gallery-settings">
			<h2><?php _e( 'Gallery Settings' ); ?></h2>

			<input type="hidden" name="link-to" value="0" data-setting="urlbutton" />

			<input type="hidden" name="columns" value="none" data-setting="columns" />

			<label class="setting">
				<span><?php _e( 'Autoplay', 'mcguffin' ); ?></span>
				<input type="checkbox" data-setting="autoplay" />
			</label>

			<label class="setting">
				<span><?php _e( 'Seconds per Slide', 'mcguffin' ); ?></span>
				<input type="text" data-setting="interval" style="float:none;width:80px;" />
			</label>

			<label class="setting size">
				<span><?php _e( 'Size' ); ?></span>
				<select class="size" name="size"
					data-setting="size"
					<# if ( data.userSettings ) { #>
						data-user-setting="imgsize"
					<# } #>
					>
					<?php
					/** This filter is documented in wp-admin/includes/media.php */
					$size_names = apply_filters( 'image_size_names_choose', array(
						'thumbnail' => __( 'Thumbnail' ),
						'medium'    => __( 'Medium' ),
						'large'     => __( 'Large' ),
						'full'      => __( 'Full Size' ),
					) );

					foreach ( $size_names as $size => $label ) : ?>
						<option value="<?php echo esc_attr( $size ); ?>">
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</script>

		<?php
	}

}
