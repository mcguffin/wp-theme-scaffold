<?php


namespace McGuffin\Media;

use McGuffin\Core;

class Upload extends Core\Singleton {

	protected function __construct() {
		add_filter( 'post_mime_types', array( $this, 'enable_documents_upload' ) );
		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
	}

	function enable_documents_upload( $post_mime_types ) {

		$post_mime_types['application/pdf'] = array( __( 'PDFs' , 'repeatcampus-admin' ), __( 'Manage PDFs' , 'repeatcampus-admin'), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' , 'repeatcampus-admin') );
		$post_mime_types['text/vcard'] = array( __( 'vCards' , 'repeatcampus-admin' ), __( 'Manage vCards' , 'repeatcampus-admin'), _n_noop( 'PDF <span class="count">(%s)</span>', 'vCards <span class="count">(%s)</span>' , 'repeatcampus-admin') );

		return $post_mime_types;
	}

	function upload_mimes( $mime_types ) {
		$mime_types['vcf'] = 'text/vcard';
		return $mime_types;
	}

}