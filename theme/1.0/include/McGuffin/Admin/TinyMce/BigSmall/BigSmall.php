<?php

namespace McGuffin\Admin\TinyMce\BigSmall;

use McGuffin\Admin\TinyMce;

class BigSmall extends TinyMce\TinyMce {

	protected $module_name = 'bigsmall';

	protected $editor_buttons = array(
		'mce_buttons'	=> array(
			'small'	=> 3,
			'big'	=> 4,
		),
	);

	protected $toolbar_css = true;

	/**
	 * Private constructor
	 */
	protected function __construct() {
		$this->plugin_params = array(
			'l10n' => array(
				'small'	=> __( 'Small', '{{theme_slug_dash}}' ),
				'big'	=> __( 'Big', '{{theme_slug_dash}}' ),
			),
		);
		parent::__construct();
	}
}
