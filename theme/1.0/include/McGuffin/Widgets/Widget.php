<?php

namespace McGuffin\Widgets;
use McGuffin;
use McGuffin\Core;

class Widget extends \WP_Widget {

	protected $fields	= null;

	protected $defaults	= '';

	static function register() {
		register_widget( get_called_class() );
	}

	/**
	 *	Widget constructor
	 *
	 *	@param	string	$widget_id
	 *	@param	string	$name
	 *	@param	assoc	$options
	 *	@param	assoc	$fields
	 */
	function __construct( $widget_id, $name, $options, $fields = array() ) {
		parent::__construct( $widget_id, $name, $options );

		$this->fields = $fields;
		$theme = McGuffin\Theme::instance();

		foreach ( $this->fields as $name => $field ) {
			
			if ( is_numeric( $name ) ) {
				continue;
			}
			
			$did_icon = $did_rte = $did_color = $did_file = false;
			$field = $this->fields[ $name ] = wp_parse_args( $field, array(
				'form_cb'	=> array( $this, 'input_' . $field['type'] ),
				'default'	=> '',
				'width'		=> '100%',
				'attr'		=> array(),
			) );

			$this->defaults[ $name ] = $field['default'];

			switch ( $field[ 'type' ] ) {
				case 'color':
					if ( ! $did_color ) {
//						wp_enqueue_script( 'wp-color-picker' );
						add_action('customize_controls_enqueue_scripts', array( $this, 'enqueue_color_picker' ));
						$did_color = true;
					}
						
					break;
				case 'checkbox':
					if ( ! isset( $field[ 'sanitize_cb' ] ) || ! is_callable( $field[ 'sanitize_cb' ] ) || ! current_user_can( 'unfiltered_html' ) ) {
						$this->fields[ $name ][ 'sanitize_cb' ] = 'isset';
					}
					break;
				case 'file':
					if ( is_admin() && ! $did_file ) {
						wp_enqueue_media();
						$this->fields[ $name ][ 'sanitize_cb' ] = 'intval';
						$this->fields[ $name ] = wp_parse_args( $this->fields[ $name ], array( 'mime_type' => '' ) );
						$did_file = true;
					}
					break;
				case 'icon':
					if ( ! $did_icon && is_admin() ) {
						wp_enqueue_style( 'repeatmobile-icon-field', $theme->getAssetUrl( '/inc/widgets/repeatmobile-icon-field.css' ) );
						wp_enqueue_style( 'repeatmobile-iconfont', $theme->getAssetUrl( '/iconfonts.css' ) );

						$this->icons = apply_filters( 'theme_iconset', array(
							'dashicons dashicons-menu' => __('Menu','mcguffin'),
							'dashicons dashicons-admin-site'	=> __('Admin Site','mcguffin'),
							'dashicons dashicons-dashboard'		=> __('Dashboard','mcguffin'),
							'dashicons dashicons-admin-post'	=> __('Admin Post','mcguffin'),
							'dashicons dashicons-admin-media'	=> __('Admin Media','mcguffin'),
							// ...
						) );
						$did_icon = true;
					}
					break;
				case 'rte':
					// check for black studio tmce presence
					if ( is_admin() ) {
						if ( ! isset( $field[ 'sanitize_cb' ] ) || ! is_callable( $field[ 'sanitize_cb' ] ) || ! current_user_can( 'unfiltered_html' ) ) {
							$this->fields[ $name ][ 'sanitize_cb' ] = 'wp_filter_post_kses';
						}
						if ( ! $did_rte ) {
							add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'rte_enabled_pages' ) );
							add_filter( 'black_studio_tinymce_container_selectors', array( $this, 'input_rte_bstw_container_selectors' ) );
							$did_rte = true;
						}
					}
					break;
				case 'cf7':
				case 'contactform':
					break;
				case 'select':
					break;
				case 'text':
					break;
				case 'textarea':
					break;
				case 'number':
					break;
				case 'maps':
					$this->fields[ $name ] = wp_parse_args( $this->fields[ $name ], array( 'marker' => true ) );
					$api_url = 'https://maps.googleapis.com/maps/api/js?v=3&key=' . get_option( 'onepager_google_maps_api_key' );
					wp_enqueue_script( 'google-maps-js-api', $api_url );
					break;
			}
		}
	}

	function enqueue_maps(){
		wp_enqueue_script( 'google-maps-js-api' );
	}

	function enqueue_color_picker(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 *	Compatibility with Black Studio TinyMCE widget
	 *
	 *	@param	array	$pages
	 *
	 *	@filter black_studio_tinymce_enable_pages
	 */
	function rte_enabled_pages( $pages ) {
		$pages[] = 'post.php';
		$pages[] = 'post-new.php';
		return $pages;
	}

	/**
	 *	Compatibility with Black Studio TinyMCE widget
	 *
	 *	@param	array	$selectors
	 *
	 *	@filter black_studio_tinymce_container_selectors
	 */
	function input_rte_bstw_container_selectors( $selectors ) {
		$selectors[] = '.repeatmobile-widget-rte';
		return array_unique( $selectors );
	}

	/**
	 *	Render Form
	 *
	 *	@param	assoc	$instance
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults);

		echo '<div class="repeatmobile-widget-form wp-clearfix">';
		foreach ( $this->fields as $name => $field ) {
			if ( is_string( $field ) ) {
				echo $field;
			} else if ( is_array( $field ) && is_callable( $field[ 'form_cb' ] ) ) {
				printf( '<div class="repeatmobile-widget-fieldwrap repeatmobile-input-type-%s repeatmobile-input-name-%s" style="width:%s;">', $field[ 'type'], $name, $field[ 'width'] );
				call_user_func( $field[ 'form_cb' ], $name, $field, $instance );
				echo '</div>';
			}
		}
		echo '</div>';
	}

	/**
	 *	Sanitize instance before saving
	 *
	 *	@param	assoc	$new_instance
	 *	@param	assoc	$old_instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		foreach ( $this->fields as $name => $field ) {
			if ( is_callable( $field['sanitize_cb'] ) ) {
				$instance[ $name ] = call_user_func( $field['sanitize_cb'], $new_instance[ $name ] );
			} else {
				$instance[ $name ] = $new_instance[ $name ];
			}
		}
		return $instance;
	}

	/**
	 *	Print an icon
	 *
	 *	@param	string	$icon_class
	 *	@param	assoc	$attr	Tag Attributes
	 */
	protected function output_cf7( $form_id, $title ) {
		echo do_shortcode( sprintf( '[contact-form-7 id="%d" title="%s"]', $form_id, esc_attr( $title ) ) );
	}

	
	/**
	 *	Print an icon
	 *
	 *	@param	string	$icon_class
	 *	@param	assoc	$attr	Tag Attributes
	 */
	protected function output_icon( $icon_class, $attr = array() ) {
		$attr = wp_parse_args( $attr, array(
			'class'	=> '',
		) );
		$attr['class'] .= ' '.$icon_class;
		printf( '<span %s></span>', $this->mk_attr( $attr ) );
	}

	
	/**
	 *	Print an icon
	 *
	 *	@param	string	$icon_class
	 *	@param	assoc	$attr	Tag Attributes
	 */
	protected function output_maps( $maps_query, $attr = array() ) {
		$attr = wp_parse_args( $attr, array(
			'class'	=> '',
		) );
		$url = sprintf( 'https://www.google.com/maps/embed/v1/place?q=%s&key=%s', 
			urlencode( $maps_query ),
			get_option( 'repeatcampus_googlemaps_apikey' )
		);
		?><div class="fixed-aspectratio aspectratio-4x3 scrolltoggle off"><?php

		$tpl = '<iframe width="600" height="450" frameborder="0" style="border:0" src="%s" allowfullscreen></iframe>';
		printf( $tpl, esc_attr( $url ) );

		?></div><?php
	}
	/**
	 *	Print an icon
	 *
	 *	@param	string	$content
	 *	@param	assoc	$attr	Tag Attributes
	 */
	protected function output_rte( $content, $attr = array() ) {
		global $wp_embed;
		$attr = wp_parse_args( $attr, array(
			'class'	=> '',
		) );

		printf( '<div %s>', $this->mk_attr( $attr ) );

		$functions = array(
			// wp core content filters
			'wptexturize',
//			'convert_smilies',
			'wpautop',
			'shortcode_unautop',
//			'prepend_attachment',
			'wp_make_content_images_responsive',
			array( $wp_embed, 'run_shortcode' ),
			array( $wp_embed, 'autoembed' ),
			'do_shortcode',
		);

		foreach ( $functions as $func ) {
			if ( is_callable( $func ) ) {
				$content = call_user_func( $func, $content );
			}
		}

		echo $content;

		?></div><?php
	}
	
	protected function output_title( $title, $args, $title_tag = 'h3' ) {
		if ( ! empty( $title ) ) {
			$title = apply_filters( 'widget_title', $title );
			printf( '<%s class="widget-title h4">%s%s%s</%s>', $title_tag, $args['before_title'], $title, $args['after_title'], $title_tag );
		}
	}


	/**
	 *	Contact Form 7 input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_cf7( $field_name, $field, $instance ) {
		$opts = array();
		$cf7_posts = get_posts( array(
			'post_type'	=> 'wpcf7_contact_form',
			'numberposts' => -1,
			'order' => 'ASC',
			'orderby'	=> 'title',
		) );
		
		foreach ( array_values( $cf7_posts ) as $cf7 ) {
			$opts[ $cf7->ID ] = $cf7->post_title;
		}
		
		return $this->input_select( $field_name, array( 'options' => $opts ) );
	}

	/**
	 *	Checkbox input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_checkbox( $field_name, $field, $instance ) {
		?>
			<input type="checkbox" <?php checked( $instance[ $field_name ], true ); ?> name="<?php echo $this->get_field_name( $field_name ); ?>" id="<?php echo $this->get_field_id( $field_name ); ?>" value="1">
			<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo $field['name'] ?></label>
		<?php
	}

	/**
	 *	Color selector
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_color( $field_name, $field, $instance ) {
		$field_id = $this->get_field_id( $field_name );
		$field = wp_parse_args( $field, array( 'palettes' => false ) );
		?>
			<label for="<?php  echo $field_id; ?>"><?php echo $field['name'] ?></label>
			<input id="<?php echo $field_id; ?>" type="text" name="<?php echo $this->get_field_name( $field_name ) ?>" class="color-picker" value="<?php echo $instance[ $field_name ] ?>" />
			<script type="text/javascript">
				(function($){
					$('input#<?php echo $field_id ?>').wpColorPicker({
						hide:true,
						palettes: <?php echo json_encode( $field['palettes'] ) ?>
					});
				})(jQuery);
			</script>
		<?php
	}

	/**
	 *	File select
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_file( $field_name, $field, $instance ) {
		$field_id = $this->get_field_id( $field_name );
		$mime = $field['mime_type'];
		?>
			<label for="<?php echo $this->get_field_id( $field_name ); ?>-btn"><?php echo $field[ 'name' ] ?></label> 
			<button id="<?php echo $field_id ?>-btn" class="select-media button-secondary"><?php _e('Select Media','mcguffin') ?></button>
			<input type="hidden" name="<?php echo $this->get_field_name( $field_name ) ?>" id="<?php echo $field_id ?>" value="<?php echo $instance[ $field_name ] ?>" />

			<div id="<?php echo $field_id ?>-thumb" class="thumbnail"> </div>
			<script type="text/javascript">
				(function($){
					var frame = null,
						inpSel = '#<?php echo $field_id ?>',
						btnSel = '#<?php echo $field_id ?>-btn',
						thmSel = '#<?php echo $field_id ?>-thumb',
						mime   = '<?php echo $mime ?>';

					$(btnSel).on( 'click', function( e ) {
						if ( frame === null ) {
							var frame_opts = {
								title: '<?php _e('Select Upload','mcguffin') ?>',
								button: { text: '<?php _e('Done','mcguffin') ?>', close: true }
							};
							if ( mime !== '' ) {
								frame_opts.library = { type: mime };
							}
							frame = wp.media(frame_opts);
							frame.on( 'select', function(){
								var attachment = frame.state().get('selection').first().attributes, 
									url;
								$(inpSel).val( attachment.id ).trigger('change');
							});
						}
						frame.open();
						e.preventDefault();
						return false;
					});

					$(inpSel).on('change', function() {
						var attachment = new wp.media.model.Attachment({id: $(this).val() });
						attachment.once( 'change', function() {
							var _filename, url;
							if ( !! attachment.get('sizes') ) {
								try {
									url = attachment.get('sizes').thumbnail.url;
								} catch ( e ) {
									// We'll use the full image instead
									url = attachment.get('sizes').full.url;
								}
							} else {
								url = attachment.get('icon');
							}

							_filename = new wp.media.View( { tagName:'span', className: 'filename' } );
							_filename.$el.text( attachment.get('title') );

							$(thmSel).html('').append(
								$('<img />').attr('src',url), 

								new wp.media.view.Button({ 
									text: '<?php _e('Remove', 'mcguffin' ); ?>', 
									click: function() {
										$(inpSel).removeAttr( 'value' );
										$(inpSel).val( '' );
										$(thmSel).html('');
									}
								}).$el.addClass('dashicons dashicons-dismiss'),
								
								_filename.$el
							
							);
						});
						attachment.fetch();
					}).trigger('change');

				})(jQuery);

			</script>
		<?php
	}

	/**
	 *	Hidden input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_hidden( $field_name, $field, $instance ) {
		?><input name="<?php echo $this->get_field_name( $field_name ); ?>" id="<?php echo $this->get_field_id( $field_name ); ?>" type="hidden" /><?php 
	}
	

	/**
	 *	Icon selector
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_icon( $field_name, $field, $instance ) {
		$value = $instance[ $field_name ];
		?><hr />
		<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php esc_html( $field['name'] ) ?></label><?php

		?>
		<input checked="checked" type="radio" id="<?php echo $this->get_field_id( $field_name . '-grid-view' ); ?>" name="<?php echo $this->get_field_name( $field_name . '-icon-select-display' ); ?>" value="grid" />
		<label class="dashicons-before dashicons-grid-view" for="<?php echo $this->get_field_id( $field_name . '-grid-view' ); ?>" ><?php _e( 'Grid View', 'mcguffin' ); ?></label>

		<input type="radio" id="<?php echo $this->get_field_id( $field_name . '-list-view' ); ?>" name="<?php echo $this->get_field_name( $field_name . '-icon-select-display' ); ?>" value="list" />
		<label class="dashicons-before dashicons-list-view" for="<?php echo $this->get_field_id( $field_name . '-list-view' ); ?>" ><?php _e( 'List View', 'mcguffin' ); ?></label>
		
		<hr /><?php

		?><p class="iconwidget-select-icon"><?php
		foreach ( $this->icons as $icon_class => $icon_name ) {
			?>
				<input type="radio" <?php checked( $icon_class, $value ); ?> name="<?php echo $this->get_field_name( $field_name ); ?>" id="<?php echo $this->get_field_id( $field_name.'-'.$icon_class ); ?>" value="<?php esc_attr_e( $icon_class ); ?>">
				<label title="<?php echo $icon_name ?>" for="<?php echo $this->get_field_id( $field_name.'-'.$icon_class ); ?>"><span class="icon <?php esc_attr_e( $icon_class ); ?>"></span><span class="icon-name"><?php echo $icon_name ?></span></label>
			<?php
		}
		?></p><?php
	}

	/**
	 *	radio button group
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_maps( $field_name, $field, $instance ) {
		$input_id = $this->get_field_id( $field_name );
		$apikey = get_option( 'onepager_google_maps_api_key' );
		$field_name_attr = $this->get_field_name( $field_name );
		$marker = $this->fields[$field_name]['marker'];
		
		$instance[ $field_name ] = wp_parse_args( $instance[ $field_name ], array(
			'query'	=> '',
			'lat'	=> 45,
			'lng'	=> 0,
			'zoom'	=> 10,
		));
		

		?>
			<label><?php echo esc_html( $field['name'] ) ?></label>

			<input class="widefat" 
				data-maps-el="#<?php echo $input_id; ?>-map" 
				id="<?php echo $input_id; ?>-query" 
				name="<?php echo $field_name_attr ?>[query]" 
				type="text" 
				value="<?php echo esc_attr( $instance[ $field_name ]['query'] ); ?>" 
				/>

			<input data-maps-el="#<?php echo $input_id; ?>-map" 
				id="<?php echo $input_id; ?>-lat" 
				name="<?php echo $field_name_attr ?>[lat]" 
				type="hidden" 
				value="<?php echo esc_attr( $instance[ $field_name ]['lat'] ); ?>" 
				/>
			<input data-maps-el="#<?php echo $input_id; ?>-map" 
				id="<?php echo $input_id; ?>-lng" 
				name="<?php echo $field_name_attr ?>[lng]" 
				type="hidden" 
				value="<?php echo esc_attr( $instance[ $field_name ]['lng'] ); ?>" 
				/>
			<input data-maps-el="#<?php echo $input_id; ?>-map" 
				id="<?php echo $input_id; ?>-zoom" 
				name="<?php echo $field_name_attr ?>[zoom]" 
				type="hidden" 
				value="<?php echo esc_attr( $instance[ $field_name ]['zoom'] ); ?>" 
				/>
			<div style="width:100%;height:400px;" id="<?php echo $input_id; ?>-map"></div>
			
			<script type="text/javascript">

			(function($){

				var el = $('#<?php echo $input_id; ?>-map').get(0),
					map, marker,
					geocoder = new google.maps.Geocoder(),
					$lng = $( '#<?php echo $input_id; ?>-lng' ),
					$lat = $( '#<?php echo $input_id; ?>-lat' ),
					$zoom = $( '#<?php echo $input_id; ?>-zoom' ),
					lng = parseFloat( $lng.val() ),
					lat = parseFloat( $lat.val() ),
					zoom = parseInt(  $zoom.val() ),
					searchTimeout = null;

				map = new google.maps.Map( el, {
					center: {
						lat: lat,
						lng: lng,
					},
					zoom: zoom,
					scrollwheel: false
				});
<?php if ( $marker ) { ?>
				marker = new google.maps.Marker({
					position:{
						lat: lat,
						lng: lng,
					},
					visible:true,
					map:map
				});
<?php } ?>
				map.addListener('zoom_changed',function(){
					$zoom.val( map.getZoom() );
				});;
				map.addListener('center_changed',function(){
					var center = map.getCenter();
					$lat.val( center.lat() );
					$lng.val( center.lng() );
				});

				$(document).on( 'change keyup', '#<?php echo $input_id; ?>-query', function() {
					var $self = $(this), addr = $(this).val();
					if ( !! searchTimeout ) {
						clearTimeout( searchTimeout );
						searchTimeout = null;
					}
					if ( ! addr ) {
						return;
					}
					searchTimeout = setTimeout(function(){
						geocoder.geocode( { 'address': addr }, function( results, status ){
							console.log(results);
							if ( status == 'OK' ) {
								map.setCenter( results[0].geometry.location );
								!! results[0].geometry.bounds && map.fitBounds( results[0].geometry.bounds );
								$self.val( results[0].formatted_address );
<?php if ( $marker ) { ?>
								marker.setPosition( results[0].geometry.location );
								marker.setVisible( true );
<?php } ?>
							} else {
								marker.setVisible( false );
							}
						clearTimeout( searchTimeout );
						searchTimeout = null;
						});
					},500);

				});
			
			})(jQuery);
			
			</script>
		<?php
	}

	/**
	 *	radio button group
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_radio( $field_name, $field, $instance ) {
		?><label><?php echo esc_html( $field['name'] ) ?></label><?php
		foreach ( $field['options'] as $option => $label ) {
			?>
			<input type="radio" name="<?php echo $this->get_field_name( $field_name ) ?>" id="<?php echo $this->get_field_id( $field_name.'-'.$option ) ?>" value="<?php echo esc_attr( $option ) ?>" <?php checked( $option, $instance[ $field_name ], true ); ?> />
			<label for="<?php echo $this->get_field_id( $field_name.'-'.$option ) ?>"><?php echo esc_html( $label ) ?></label>
			<?php
		}
	}

	/**
	 *	Rich text editor
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_rte( $field_name, $field, $instance ) {
		?><div class="widget-rte"><?php

		$input_id = 	str_replace('-','_',$this->get_field_id( $field_name ) . '_' . dechex( rand(0xf0000000,0xffffffff) ) );
		$input_name =	$this->get_field_name( $field_name );
		$text = isset($instance[ $field_name ] ) ? $instance[ $field_name ] : '';

		do_action( 'black_studio_tinymce_editor', $text, $input_id, $input_name );

		?>
		<input type="hidden" id="widget-black-studio-tinymce-<?php echo $input_id ?>-type" value="visual" />
		<script type="text/javascript">
			// init
			(function( $ ) {
				var id = '<?php echo $input_id ?>';
				(typeof bstw !== 'undefined' ) && bstw(id).set_mode('visual').activate();
			})(jQuery);
		</script><?php
		
		do_action( 'black_studio_tinymce_after_editor' );
		?></div><?php
	}

	/**
	 *	Select menu
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_select( $field_name, $field, $instance ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo $field['name'] ?></label> 
			
			<select class="_widefat" id="<?php echo $this->get_field_id( $field_name ); ?>" name="<?php echo $this->get_field_name( $field_name ); ?>">
			<?php
			foreach ( $field['options'] as $option => $label ) {
				?>
				<option value="<?php echo esc_attr( $option ) ?>"  <?php selected( $option, $instance[ $field_name ], true ); ?>><?php echo esc_html( $label ) ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<?php 
	}

	/**
	 *	Text input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_text( $field_name, $field, $instance ) {
		$attr = wp_parse_args( $field['attr'], array(
			'id'	=> $this->get_field_id( $field_name ),
			'name'	=> $this->get_field_name( $field_name ),
			'value'	=> esc_attr( $instance[ $field_name ] ),
			'class'	=> 'widefat',
			'type'	=> 'text',
		));
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo $field[ 'name' ] ?></label> 
			<input <?php echo $this->mk_attr( $attr ); ?> />
		</p>
		<?php 
	}


	/**
	 *	Text input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_number( $field_name, $field, $instance ) {
		$attr = wp_parse_args( $field['attr'], array(
			'id'	=> $this->get_field_id( $field_name ),
			'name'	=> $this->get_field_name( $field_name ),
			'value'	=> esc_attr( $instance[ $field_name ] ),
			'class'	=> 'widefat',
			'type'	=> 'number',
		));
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo $field[ 'name' ] ?></label> 
			<input <?php echo $this->mk_attr( $attr ); ?> />
		</p>
		<?php 
	}
	/**
	 *	Textarea input
	 *
	 *	@param	string	$field_name
	 *	@param	assoc	$field
	 *	@param	assoc	$instance
	 */
	protected function input_textarea( $field_name, $field, $instance ) {
		$attr = wp_parse_args( $field['attr'], array(
			'id'	=> $this->get_field_id( $field_name ),
			'name'	=> $this->get_field_name( $field_name ),
			'class'	=> 'widefat',
		));
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $field_name ); ?>"><?php echo $field[ 'name' ] ?></label> 
			<textarea <?php echo $this->mk_attr( $attr ); ?>><?php echo esc_textarea( $instance[ $field_name ] ); ?></textarea>
		</p>
		<?php 
	}

	/**
	 *	Make attributes string
	 *
	 *	@param	assoc	$attr
	 */
	protected function mk_attr( $attr = array() ) {
		$attrs = array();
		foreach ( $attr as $k => $v ) {
			if ( $v === '' ) {
				$attrs[] = sanitize_title( $k );
			} else if ( $v !== false ) {
				$attrs[] = sprintf( '%s="%s"', sanitize_title( $k ), esc_attr( $v ) );
			}
		}
		return implode(' ', $attrs );
	}
	
}

