<?php

namespace McGuffin\PostType;

use McGuffin\Core;

class PostTypes extends Core\Singleton {


	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		// Refer to Plugin Sortable Posts by Carlos Rios
		add_filter( 'pre_option_sortable_posts', array( &$this, 'sortable_post_types') );
		add_filter( 'pre_option_sortable_taxonomies', array( &$this, 'sortable_taxonomies') );
//		add_filter( 'sortable_posts_settings', '__return_false' );

		add_filter( "get_next_post_where", array( $this, 'adjacent_where_clause_next' ), 10, 5 );
		add_filter( "get_previous_post_where", array( $this, 'adjacent_where_clause_prev' ), 10, 5 );
		add_filter( "get_next_post_sort", array( $this, 'adjacent_by_menu_order_clause_next' ), 10, 2 );
		add_filter( "get_previous_post_sort", array( $this, 'adjacent_by_menu_order_clause_prev' ), 10, 2 );

		add_action( 'init' , array( $this , 'register_taxonomies' ) );
	}

	function adjacent_where_clause_prev( $clause, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		if ( ! is_admin() && $post->post_type == 'project' ) {
			return $this->adjacent_where_clause( $post, '<' );
		}
		return $clause;
	}
	function adjacent_where_clause_next( $clause, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		if ( ! is_admin() && $post->post_type == 'project' ) {
			return $this->adjacent_where_clause( $post, '>' );
		}
		return $clause;
	}
	
	private function adjacent_where_clause( $post, $op ) {
		global $wpdb;
		return $wpdb->prepare( "WHERE p.menu_order $op %d AND p.post_type = %s", $post->menu_order,  $post->post_type );
	}
	
	function adjacent_by_menu_order_clause_prev( $clause, $post ) {
		if ( $post->post_type == 'project' ) {
			return "ORDER BY p.menu_order DESC LIMIT 1";
		}
		return $clause;
	}
	function adjacent_by_menu_order_clause_next( $clause, $post ) {
		if ( $post->post_type == 'project' ) {
			return "ORDER BY p.menu_order ASC LIMIT 1";
		}
		return $clause;
	}
	function sortable_post_types( $post_types ) {
		return array( 'project' );
	}

	
	private function hide_post_type( $post_type , $slug ) {
		global $wp_post_types;
		if ( isset( $wp_post_types[ $post_type ] ) ) {
			$wp_post_types[ $post_type ]->public = false;
			$wp_post_types[ $post_type ]->show_ui = false;
			$wp_post_types[ $post_type ]->show_in_menu = false;
			$wp_post_types[ $post_type ]->show_in_nav_menus = false;
			$wp_post_types[ $post_type ]->show_in_admin_bar = false;
			$wp_post_types[ $post_type ]->can_export = false;
			$wp_post_types[ $post_type ]->has_archive = false;
			$wp_post_types[ $post_type ]->rewrite = false;
			$wp_post_types[ $post_type ]->query_var = false;
			$wp_post_types[ $post_type ]->map_meta_cap = false;
			if ( ! defined( 'DOING_AJAX' ) ) {
				$slug = ( !$slug ) ? 'edit.php?post_type=' . $post_type : $slug;
				remove_menu_page( $slug );
			}
		}
	}
	private function unregister_post_type( $post_type , $slug ) {
		global $wp_post_types;
		if ( isset( $wp_post_types[ $post_type ] ) ) {
			unset( $wp_post_types[ $post_type ] );
			if ( ! defined( 'DOING_AJAX' ) ) {
				$slug = ( !$slug ) ? 'edit.php?post_type=' . $post_type : $slug;
				remove_menu_page( $slug );
			}
		}
	}
}
