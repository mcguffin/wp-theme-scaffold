<?php 

namespace McGuffin\Widgets;


class EditorWidget extends Widget {
	private $tag_opts = null;

	private $class_opts = null;

	/**
	 *	Widget constructor
	 */
	function __construct() {
		parent::__construct( 
			'kdc_editor_widget',
			__( 'Editor', 'mcguffin' ), 
			array( 'description' => __( 'Rich text editor', 'mcguffin' ), ),
			array(
				'title'	=> array(
					'type'	=> 'text',
					'name'	=> __('Title', 'mcguffin' ),
				),
				'content'	=> array(
					'type'	=> 'rte',
					'name'	=> __('Content', 'mcguffin' ),
				),
			)
		);
	}

	/**
	 *	Render Widget
	 *
	 *	@param	assoc	$args
	 *	@param	assoc	$instance
	 */
	function widget( $args, $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults);

		echo $args['before_widget'];

//		$this->output_title( $instance['title'], $args );

		$this->output_rte( $instance['content'], array( 'class' => 'editor-content' ) );

		echo $args['after_widget'];
	}
}

