<?php


namespace McGuffin\Media;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use McGuffin\Core;

class Upload extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'post_mime_types', array( $this, 'enable_documents_upload' ) );
		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
	}

	/**
	 *	@filter post_mime_types
	 */
	function enable_documents_upload( $post_mime_types ) {

		$post_mime_types['application/pdf'] = array( __( 'PDFs' , 'mcguffin' ), __( 'Manage PDFs' , 'mcguffin'), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' , 'mcguffin') );
		$post_mime_types['text/vcard'] = array( __( 'vCards' , 'mcguffin' ), __( 'Manage vCards' , 'mcguffin'), _n_noop( 'PDF <span class="count">(%s)</span>', 'vCards <span class="count">(%s)</span>' , 'mcguffin') );

		return $post_mime_types;
	}

	/**
	 *	@filter upload_mimes
	 */
	function upload_mimes( $mime_types ) {
		$mime_types['vcf'] = 'text/vcard';
		return $mime_types;
	}

}
