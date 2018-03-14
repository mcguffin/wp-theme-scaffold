<?php

namespace McGuffin\Admin\TinyMce\Uppercase;

use McGuffin\Admin\TinyMce;

class Uppercase extends TinyMce\TinyMce {

	protected $module_name = 'uppercase';

	protected $editor_buttons = array(
		'mce_buttons_2'	=> array(
			'uppercase'	=> 3,
		),
	);

	protected $toolbar_css = true;
	protected $text_widget = true;

	/**
	 * Private constructor
	 */
	protected function __construct() {
		$this->plugin_params = array(
			'l10n' => array(
				'uppercase'	=> __( 'Uppercase', 'mcguffin' ),
			),
		);
		parent::__construct();
	}
}
