<?php

namespace McGuffin\Widgets;

use McGuffin\Core;

class Widgets extends Core\Singleton {

	protected function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

	}
	function widgets_init() {
		
		register_sidebar( array(
			'name'          => esc_html__( 'Footer', 'polyplanet'),
			'id'            => 'footer',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="h4 widget-title">',
			'after_title'   => '</h2>',
		) );

		while ( have_rows ('widgets', 'general' ) ) {
			the_row();
			register_widget( new GenericWidget( get_sub_field('id-base'), get_sub_field('name') ) );
		}
	}

	function widget_title_shy( $title ) {
		return str_replace('--', '&shy;', $title );
	}

}
