<?php

namespace McGuffin\Admin\TinyMce\Bootstrap;

use McGuffin\Admin\TinyMce;

class Bootstrap extends TinyMce\TinyMce {

	protected $module_name = 'bootstrap';
	
	protected $editor_buttons = array(
		'mce_buttons_2' => array(
			'bootstrap'	=> 1,
		),
	);
	

	/**
	 * Private constructor
	 */
	protected function __construct() {
		/*
		h*, p, address: 
			.text-left
			.text-center
			.text-right
			.text-justify
			.text-nowrap
		
		a, button
			btn-lg, btn-sm, btn-xs
				btn-block
		
		img
			.img-rounded
			.img-circle
			.img-thumbnail
		
		span
			label label-default
			label label-primary
			label label-success
			label label-info
			label label-warning
			label label-danger
		
		*/

		$bootstrap_classes = array(
			'a,button'	=> array(
				'title'		=> __( 'Link style', '{{theme_slug_dash}}' ),
				'selector'	=> 'A',
				'classes'	=> array(
					array( 'value' => '', 					'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-default',	'text' => __( 'Button Default', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-primary',	'text' => __( 'Button Primary', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-success', 	'text' => __( 'Button Success', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-info', 		'text' => __( 'Button Info', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-warning', 	'text' => __( 'Button Warning', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'btn btn-danger', 	'text' => __( 'Button Danger', '{{theme_slug_dash}}' ), ),
				),
			),
			'h1,h2,h3,h4,h5,h6'	=> array(
				'title'		=> __( 'Heading style', '{{theme_slug_dash}}' ),
				'selector'	=> 'H1,H2,H3,H4,H5,H6',
				'classes'	=> array(
					array( 'value' => '', 	'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h1', 'text' => __( 'H1', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h2', 'text' => __( 'H2', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h3', 'text' => __( 'H3', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h4', 'text' => __( 'H4', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h5', 'text' => __( 'H5', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'h6', 'text' => __( 'H6', '{{theme_slug_dash}}' ), ),
				),
			),
			'p' 	=> array(
				'title'		=> __( 'Paragraph style', '{{theme_slug_dash}}' ),
				'selector'	=> 'P',
				'classes'	=> array(
					array( 'value' => '', 						'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'lead',					'text' => __( 'Lead', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'alert alert-success',	'text' => __( 'Alert Success', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'alert alert-info',		'text' => __( 'Alert Info', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'alert alert-warning',	'text' => __( 'Alert Warning', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'alert alert-danger',		'text' => __( 'Alert Danger', '{{theme_slug_dash}}' ), ),
					
				),
			),
			'ul,ol'	=> array(
				'title'		=> __( 'List style', '{{theme_slug_dash}}' ),
				'selector'	=> 'UL,OL',
				'classes'	=> array(
					array( 'value' => '', 				'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'list-unstyled', 	'text' => __( 'Unstyled List', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'list-inline', 	'text' => __( 'Inline List', '{{theme_slug_dash}}' ), ),
				),
			),
			'hr'	=> array(
				'title'		=> __( 'Horizontal ruler style', '{{theme_slug_dash}}' ),
				'selector'	=> 'HR',
				'classes'	=> array(
					array( 'value' => '', 		'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'small',	'text' => __( 'Small', '{{theme_slug_dash}}' ), ),
				),
			),
			'blockquote'	=>	array(
				'title'		=> __( 'Blockquote', '{{theme_slug_dash}}' ),
				'selector'	=> 'BLOCKQUOTE',
				'classes'	=> array(
					array( 'value' => '', 		'text' => __( 'Normal', '{{theme_slug_dash}}' ), ),
					array( 'value' => 'blockquote-reverse',	'text' => __( 'Reverse', '{{theme_slug_dash}}' ), ),
				),
			),
		);
		$this->plugin_params = array(
			'l10n'		=> array(
				
			),
			'classes'	=> apply_filters( 'theme_rte_custom_classes', $bootstrap_classes ),
		);
		parent::__construct();
	}

}
