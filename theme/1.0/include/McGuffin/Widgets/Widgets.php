<?php

namespace McGuffin\Widgets;

use McGuffin\Core;

class Widgets extends Core\Singleton {

	protected function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'widget_title', array( $this, 'widget_title_shy' ), 0 );

	}
	function widgets_init() {
		$core = \McGuffin\Theme::instance();
		wp_enqueue_style( '{{theme_slug_dash}}-widgets', $core->getAssetUrl( '/css/admin/widgets.css' ) );

		EditorWidget::register();

		//*
		register_sidebar( array(
			'name'          => esc_html__( 'Footer', '{{theme_slug_dash}}' ),
			'id'            => 'footer',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="h4 widget-title">',
			'after_title'   => '</h2>',
		) );
		//*/
	}

	function widget_title_shy( $title ) {
		return str_replace('--', '&shy;', $title );
	}

}
