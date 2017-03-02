<?php

namespace McGuffin\Admin\TinyMce\Clear;

use McGuffin\Admin\TinyMce;

class Clear extends TinyMce\TinyMce {

	protected $module_name = 'clear';

	protected $editor_buttons = array(
		'mce_buttons_2'	=> array(
			'clear'	=> false,
		),
	);

	protected $editor_css = true;
	protected $toolbar_css = true;

	/**
	 * Private constructor
	 */
	protected function __construct() {
		$this->plugin_params = array(
			'l10n' => array(
				'clear'			=> __( 'Clear', 'mcguffin' ),
				'insert_clear'	=> __( 'Insert Clear', 'mcguffin' ),
			),
		);
		parent::__construct();
	}
}
