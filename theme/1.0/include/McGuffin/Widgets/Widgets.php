<?php

namespace McGuffin\Widgets;

use McGuffin\Core;

class Widgets extends Core\Singleton {

	protected function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}


	public function widgets_init() {
		$core = \McGuffin\Theme::instance();

		MapWidget::register();
		MapMarkerWidget::register();

		register_sidebar( array(
			'name'          => esc_html__( 'Header', 'mcguffin'),
			'id'            => 'header',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="h4 widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Footer', 'mcguffin'),
			'id'            => 'footer',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="h4 widget-title">',
			'after_title'   => '</h2>',
		) );

	}

}
