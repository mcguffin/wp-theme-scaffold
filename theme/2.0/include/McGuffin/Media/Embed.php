<?php


namespace McGuffin\Media;

use McGuffin\Core;

class Embed extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'embed_oembed_html', array( $this, 'embed_container' ) );
	}

	/**
	 *	@filter embed_oembed_html
	 */
	public function embed_container( $embed_html ) {
		return sprintf( '<div class="embed-container fixed-aspectratio aspectratio-16x9">%s</div>', $embed_html );
	}
}
