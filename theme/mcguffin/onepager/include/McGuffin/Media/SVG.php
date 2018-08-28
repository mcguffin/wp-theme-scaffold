<?php


namespace McGuffin\Media;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use McGuffin\Core;

class SVG extends Core\Singleton {

	private $mime_type = 'image/svg+xml';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'check_filetype_and_ext' ), 10, 4 );
		add_filter( 'image_downsize', array( $this, 'image_downsize' ), 10, 3 );
	}

	/**
	 *	@filter upload_mimes
	 */
	public function upload_mimes( $mime_types ) {
		if ( current_user_can( 'unfiltered_html' ) ) {
			$mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
		}
		return $mime_types;
	}

	/**
	 *	@filter wp_check_filetype_and_ext
	 */
	public function check_filetype_and_ext( $ft_ext, $file, $filename, $mimes ) {
		extract( $ft_ext );
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );
		if ( $ext === 'svg' ) {
			$type = $this->mime_type;
			$proper_filename = $filename;
		} else {
			return $ft_ext;
		}
		return compact( 'ext', 'type', 'proper_filename' );
	}
	/**
	 *	@filter image_downsize
	 */
	public function image_downsize( $return, $id, $size  ) {
		if ( get_post_mime_type( $id ) === $this->mime_type ) {
			$img_url = wp_get_attachment_url($id);
			list( $width, $height ) = $this->get_svg_size( $id );
			$return = array( $img_url, $width, $height, false );
		}
		return $return;
	}

	/**
	 *	@param $attachment_id
	 *	@return array
	 */
	private function get_svg_size( $attachment_id ) {
		$file = get_attached_file( $attachment_id );
		$contents = file_get_contents( $file );
		$width = $height = false;

		preg_match( '/width=["\'](\w+)["\']/i', $contents, $match_wd );
		preg_match( '/height=["\'](\w+)["\']/i', $contents, $match_hg );

		if ( isset( $match_wd[1] ) ) {
			$width = intval( $match_wd[1] );
		}
		if ( isset( $match_hg[1] ) ) {
			$height = intval( $match_hg[1] );
		}
		if ( ! $width || ! $height ) {
			preg_match( '/viewBox=["\'](\d+)\s(\d+)\s(\d+)\s(\d+)["\']/i', $contents, $match_vb );
			if ( isset( $match_vb[3], $match_vb[4] ) ) {
				$width	= intval( $match_vb[3] );
				$height	= intval( $match_vb[4] );
			}
		}
		return array( $width, $height );
	}

}
