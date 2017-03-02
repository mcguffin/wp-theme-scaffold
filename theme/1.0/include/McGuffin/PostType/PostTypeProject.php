<?php


/*  Copyright 2015  Jörn Lund

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace McGuffin\PostType;

use McGuffin\Core;

class PostTypeProject extends PostType {

	protected $post_type_slug = 'project';
	/**
	 * Register Post Types
	 * 
	 */
	public function register_post_types( ) {

		// register post type Case
		$project_labels = array(
			'name'                => _x( 'Projects', 'Post Type General Name', 'mcguffin' ),
			'singular_name'       => _x( 'Project', 'Post Type Singular Name', 'mcguffin' ),
			'menu_name'           => __( 'Projects', 'mcguffin' ),
			'parent_item_colon'   => __( 'Parent Item:', 'mcguffin' ),
			'all_items'           => __( 'All Projects', 'mcguffin' ),
			'view_item'           => __( 'View Project', 'mcguffin' ),
			'add_new_item'        => __( 'Add New Project', 'mcguffin' ),
			'add_new'             => __( 'Add Project', 'mcguffin' ),
			'edit_item'           => __( 'Edit Project', 'mcguffin' ),
			'update_item'         => __( 'Update Project', 'mcguffin' ),
			'search_items'        => __( 'Search Projects', 'mcguffin' ),
			'not_found'           => __( 'Not found', 'mcguffin' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'mcguffin' ),
		);
		$project_args = array(
			'label'               => __( 'Project', 'mcguffin' ),
			'description'         => __( 'Project Description', 'mcguffin' ),
			'labels'              => $project_labels,
			'supports'            => array( 'title' , 'editor', 'excerpt'  ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 22,
			'menu_icon'           => 'dashicons-images-alt',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		
		register_post_type( , $project_args );

	}
}
