<?php


namespace McGuffin\Media;

use McGuffin\Core;

class Slider extends Core\Singleton {
	protected function __construct() {
		add_filter( 'post_gallery', array( $this, 'post_gallery' ), 10, 3 );
	}

	function post_gallery( $output, $atts, $instance ) {
		$get_posts_args = shortcode_atts( array( 
			'include'			=> '',
			'post_status' 		=> 'inherit', 
			'post_type' 		=> 'attachment', 
			'post_mime_type'	=> 'image', 
			'order'				=> 'ASC', 
			'orderby'			=> 'post__in',
		), $atts );
	
		$attachments = get_posts( $get_posts_args );
		return get_{{theme_slug}}_slider( $attachments, $atts );
	}
	
	public function get_slider( $slider_items, $slider_args ) {
		$output = '';
	
		$format_image_sizes = array(
			'cinema'		=> 'cinema-lg',
			'tv'			=> 'tv-720p',
			'din-landscape'	=> 'din-landscape-large',
		);
	
		$defaults = array(
			'format'	=> 'tv', // cinema, tv, din-landscape
			'interval'	=> 5,
			'autoplay'	=> false,
			'item_tag'	=> 'div',
		);

		// apply defaults
		$slider_args = wp_parse_args( $slider_args, $defaults );

		// sanitize
		$slider_args['item_tag'] = preg_replace( '/[^a-z0-9]/i', '', $slider_args['item_tag'] );
		if ( empty( $slider_args['item_tag'] ) ) {
			$slider_args['item_tag'] = $defaults['item_tag'];
		}
		$slider_args['interval'] = floatval( $slider_args['interval'] );

		$slider_args['autoplay'] = boolval( $slider_args['autoplay'] );
		if ( ! isset( $format_image_sizes[ $slider_args['format'] ] ) ) {
			$slider_args['format'] = $defaults['format'];
		}

		if ( count( $slider_items ) ) {
			static $slider_count = 0;
			$slider_count++;
		
			$item_tag		= $slider_args['item_tag'];
			$slider_format	= $slider_args['format'];
			$image_size		= $format_image_sizes[ $slider_format ];
			$interval		= intval( $slider_args['interval'] * 1000 );
			$ride			= $slider_args['autoplay'] ? 'data-ride="carousel"' : '';
		
			$slider_id		= sprintf( 'slider-%d', $slider_count );
			$slider_items	= array_values( $slider_items );

		
			$output .= "<div id=\"{$slider_id}\" class=\"carousel slide\" {$ride} data-pause=\"hover\" data-interval=\"{$interval}\">\n";
			$output .= "\t<ol class=\"carousel-indicators\">\n";
			foreach ( $slider_items as $i => $slider_item ) {
				$output .= sprintf( "\t<li data-target=\"#%s\" data-slide-to=\"%d\" %s></li>\n", $slider_id, $i, $i==0 ? 'class="active"' : '' );
			}
			$output .= "\t</ol>\n";

			$output .= "\t<div class=\"carousel-inner\" role=\"listbox\">\n";

			foreach ( $slider_items as $i => $slider_item ) {
				$output .= sprintf( "\t\t<%s class=\"item fixed-aspectratio aspectratio-din-landscape aspectratio-sm-%s %s\">\n", $item_tag, $slider_format, $i==0 ? 'active' : '' );
				if ( $attachment_item = $this->slider_item_is( 'attachment', $slider_item ) ) {

					$output .= wp_get_attachment_image( $attachment_item->ID, 'din-landscape-small', false, array( 'class' => 'visible-xs' ) );
					$output .= wp_get_attachment_image( $attachment_item->ID, $image_size, false, array( 'class' => 'header-image hidden-xs' ) );

				} else if ( $image_item = $this->slider_item_is( 'image', $slider_item ) ) {

					$output .= wp_get_attachment_image( $image_item['id'], 'din-landscape-small', false, array( 'class' => 'visible-xs' ) );
					$output .= wp_get_attachment_image( $image_item['id'], $image_size, false, array( 'class' => 'header-image hidden-xs' ) );

	/*
				} else if ( $video_item = _{{theme_slug}}_slider_item_is( 'video', $slider_item ) ) {

					$output .= {{theme_slug}}_video( $video_item->ID );

	*/
				} else if ( $project_item = $this->slider_item_is( 'project', $slider_item ) ) {

					if ( $image_id = the_{{theme_slug}}_project_header_image_id( $project_item->ID, $image_size ) ) {
						$output .= wp_get_attachment_image( $image_id, 'din-landscape-small', false, array( 'class' => 'visible-xs' ) );
						$output .= wp_get_attachment_image( $image_id, $image_size, false, array( 'class' => 'header-image hidden-xs' ) );
					}
					$output .= "\t\t\t<div>\n";
					$output .= "\t\t\t\t<div class=\"project-header\">\n";
					$output .= sprintf("\t\t\t\t\t<h2 class=\"project-title\">%s</h2>\n", get_the_title( $project_item->ID ));

					$output .= sprintf( "\t\t\t\t\t<a class=\"btn btn-primary icon-angle-double-right\" href=\"%s\">%s</a>\n",
							get_permalink($project_item->ID),
							__('Go to project','studio-rakete') );
						
					$output .= "\t\t\t\t</div>\n";
					$output .= "\t\t\t</div>\n";
				}
				$output .= sprintf( "\t\t</%s>\n", $item_tag );
			}
			$output .= "\t</div>\n";
		

			$output .= "\t<a class=\"left carousel-control\" href=\"#{$slider_id}\" role=\"button\" data-slide=\"prev\">\n";
			$output .= "\t\t<span class=\"arrow icon-left-open-thick\" aria-hidden=\"true\"></span>\n";
			$output .= sprintf("\t\t<span class=\"sr-only\">%s</span>\n", __('Previous Slide','studio-rakete') );
			$output .= "\t</a>\n";
			$output .= "\t<a class=\"right carousel-control\" href=\"#{$slider_id}\" role=\"button\" data-slide=\"next\">\n";
			$output .= "\t\t<span class=\"arrow icon-right-open-thick\" aria-hidden=\"true\"></span>\n";
			$output .= sprintf("\t\t<span class=\"sr-only\">%s</span>\n", __('Next Slide','studio-rakete') );
			$output .= "\t</a>\n";
			
			$output .= "</div>\n";
		}
		return $output;

	}

	function slider_item_is( $what, $item ) {
	
		if ( is_numeric( $item ) ) {
			$item = get_post( $item );
		}

		if ( is_array( $item ) && isset( $item['type'] ) && $item['type'] == $what ) {
			return $item;
		} else if ( is_a( $item, 'WP_Post' ) && isset( $item->post_type ) && $item->post_type == $what ) {
			return $item;
		}
		return false;
	}

}

