<?php

namespace McGuffin\Widgets;

use McGuffin\Core;

class GenericWidget extends \WP_Widget {


	/**
	 *	Widget constructor
	 *
	 *	@param	string	$widget_id
	 *	@param	string	$name
	 *	@param	assoc	$options
	 *	@param	assoc	$fields
	 */
	function __construct( $widget_id, $name, $options = array() ) {
		parent::__construct( $widget_id, $name, $options );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		add_filter( 'current_widget_id', array( $this, 'get_widget_id') );

		get_template_part( 'template-parts/widgets/' . $this->id_base );

		remove_filter( 'current_widget_id', array( $this, 'get_widget_id') );

	}

	public function get_widget_id() {
		return $this->id;
	}

	/**
	 *	Render Form
	 *
	 *	@param	assoc	$instance
	 */
	public function form( $instance ) {
	}

	/**
	 *	Sanitize instance before saving
	 *
	 *	@param	assoc	$new_instance
	 *	@param	assoc	$old_instance
	 */
	function update( $instance, $old_instance ) {

		return $instance;
	}


}
