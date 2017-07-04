<?php

namespace McGuffin\WPGridbuilder;

use McGuffin\Core;

class Gridbuilder extends Core\Singleton {

	protected function __construct() {
		add_filter( 'gridbuilder_container_editor', array( $this, 'menu_entry_editor' ) );

		add_filter( 'gridbuilder_row_editor', array( $this, 'row_editor' ) );
		add_filter( 'gridbuilder_row_attr', array( $this, 'row_attr'), 10, 2 );

		add_filter( 'gridbuilder_widget_types', array( $this, 'widget_types' ) );
		add_filter( 'gridbuilder_background_elements', array( $this, 'background_elements'), 10, 2 );

	}
	public function background_elements( $html, $item ) {
		if ( ! empty( $html ) ) {
			$html = sprintf( '<div class="background-wrap">%s</div>', $html );
		}
		if ( $item['type'] == 'row' && $item['flyout_text'] ) {
			$html = sprintf( '<div class="flyout">%s</div>', $item['flyout_text'] ) . $html;
		}
		return $html;
	}

	public function widget_types( $widget_types ) {
		return $widget_types;
	}
	
	public function row_attr( $attr, $item ) {
		return $attr;
	}
	
	public function row_editor( $fields ) {
		/*
		colorset, placement, flyout
		*/

		return $fields;
	}

	public function menu_entry_editor( $fields ) {
		$fields[ 'menu_sep1' ] = array(
			'type'	=> 'html',
			'priority' => 11,
			'html'	=> '<hr />',
			'style'	=> 'clear:both;',
		);
		$fields[ 'menu_link' ] = array(
			'title'	=> __( 'Create menu entry', 'mcguffin' ),
			'type'	=> 'checkbox',
			'priority' => 12,
			'style'	=> 'box-sizing:border-box;float:left;padding-right:10px;width:33%;',
		);
		$fields[ 'menu_title' ] = array(
			'title'	=> __( 'Menu-Title', 'mcguffin' ),
			'type'	=> 'text',
			'priority' => 13,
			'style'	=> 'box-sizing:border-box;float:left;padding-right:10px;padding-left:10px;width:33%;',
		);
		$fields[ 'menu_parent' ] = array(
			'title'	=> __( 'Parent Menu', 'mcguffin' ),
			'type'	=> 'text',
			'priority' => 14,
			'style'	=> 'box-sizing:border-box;float:left;padding-left:10px;width:33%;',
		);
		$fields[ 'menu_sep2' ] = array(
			'type'	=> 'html',
			'priority' => 15,
			'html'	=> '<hr />',
			'style'	=> 'clear:both;',
		);
		return $fields;
	}

}