<?php

namespace McGuffin\Admin\TinyMce\Mobile;

use McGuffin\Admin\TinyMce;

class Mobile extends TinyMce\TinyMce {

	protected $module_name = 'mobile';

	protected $editor_buttons = array(
		'mce_buttons_2' => array(
			'mobile'	=> false,
		),
	);

	protected $editor_css = true;
	protected $text_widget = true;

}
