<?php

namespace McGuffin\WPGridbuilder;

use McGuffin\Core;


class AnchorNav extends Core\Singleton {
	private $_anchor_nav_link_attr;

	function get_anchor_nav_items( $grid_data, &$menuitems ) {

		if ( ! isset( $grid_data['items'] ) ) {
			return;
		}
		foreach ( $grid_data['items'] as $i => $item ) {
			if ( isset( $item[ 'menu_link' ] ) && $item[ 'menu_link' ] ) {
				$item = array(
					'ID'		=> $item[ 'attr_id' ],
					'classes'	=> array( 'menu-item' ),
					'title'		=> $item[ 'menu_title' ],
					'parent'	=> $item[ 'menu_parent' ],
					'url'		=> '#' . $item[ 'attr_id' ],
					'xfn'		=> '',
					'target'	=> '',
				);
				$menuitems[] = (object) $item;
			}
			$this->get_anchor_nav_items( $item, $menuitems );
		}
	}

	function anchor_nav_link_attr($atts, $item, $args, $depth ){
		return $atts + $this->_anchor_nav_link_attr;
	}

	function get_anchor_nav( $grid_data, $attr = false, $walker_config = array() ) {
		$menuitems = array();
		$this->get_anchor_nav_items( $grid_data, $menuitems );
		$walker = new \Walker_Nav_Menu();
		$walker->db_fields = array( 'parent' => 'parent', 'id' => 'ID' );

		$walker_args = (object) wp_parse_args( $walker_config, array(
			'before'		=> '',
			'after'			=> '',
			'link_before'	=> '',
			'link_after'	=> '',
		) );

		$output = '<ul class="menu nav anchor-nav">';

		if ( $attr ) {
			$this->anchor_nav_link_attr = $attr;
			add_filter( 'nav_menu_link_attributes', '_repeatcampus_anchor_nav_link_attr', 10, 4 );
		}
		$output	.= $walker->walk( $menuitems, 10, $walker_args );
		if ( $attr ) {
			remove_filter( 'nav_menu_link_attributes', '_repeatcampus_anchor_nav_link_attr', 10 );
		}
		$output	.= '</ul>';
		return $output;
	}


}