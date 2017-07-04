<?php

namespace McGuffin\Admin\TinyMce\Shy;

use McGuffin\Admin\TinyMce;

class Shy extends TinyMce\TinyMce {
	
	protected $module_name = 'shy';

	protected $editor_buttons = array(
		'mce_buttons' => array(
			'shy'			=> -1,
			'showinvisible'	=> -1,
		),
	);
	
	protected $toolbar_css	= true;
	protected $editor_css	= true;
	protected $text_widget = true;
	
	protected function __construct() {
		$this->plugin_params = array(
			'l10n' => array(
				'soft_hyphen'			=> __( 'Soft Hyphen', 'mcguffin' ),
				'insert_soft_hyphen'	=> __( 'Insert Soft Hyphen', 'mcguffin' ),
				'show_invisibles' 		=> __( 'Show Invisibles', 'mcguffin' ),
			),
		);

		parent::__construct();
	}
}

