<?php

/**
 * Create post types.
 */
function orbis_checklist_create_initial_post_types() {
	register_post_type(
		'orbis_checklist_item',
		array(
			'label'         => __( 'Checklist', 'orbis_checklist' ),
			'labels'        => array(
				'name'               => _x( 'Checklist items', 'post type general name', 'orbis_checklist' ),
				'singular_name'      => _x( 'Checklist item', 'post type singular name', 'orbis_checklist' ),
				'add_new'            => _x( 'Add new', 'checklist item', 'orbis_checklist' ),
				'add_new_item'       => __( 'Add new checklist item', 'orbis_checklist' ),
				'edit_item'          => __( 'Edit checklist item', 'orbis_checklist' ),
				'new_item'           => __( 'New checklist item', 'orbis_checklist' ),
				'view_item'          => __( 'View checklist item', 'orbis_checklist' ),
				'search_items'       => __( 'Search checklist items', 'orbis_checklist' ), 
				'not_found'          => __( 'No checklist ttems found', 'orbis_checklist' ),
				'not_found_in_trash' => __( 'No checklist ttems found in Trash', 'orbis_checklist' ),
				'parent_item_colon'  => __( 'Parent checklist items:', 'orbis_checklist' ),
				'menu_name'          => __( 'Checklist', 'orbis_checklist' ),
			),
			'public'        => true,
			'menu_position' => 30,
			'menu_icon'     => 'dashicons-yes',
			'supports'      => array( 'title', 'editor', 'author' ),
			'has_archive'   => true,
			'rewrite'       => array(
				'slug' => _x( 'checklist-items', 'slug', 'orbis_checklist' ),
			),
		)
	);

	register_taxonomy(
		'orbis_checklist_category',
		'orbis_checklist_item',
		array(
			'hierarchical' => true,
			'labels'       => array(
				'name'              => _x( 'Checklist categories', 'class general name', 'orbis_checklist' ),
				'singular_name'     => _x( 'Checklist category', 'class singular name', 'orbis_checklist' ),
				'search_items'      => __( 'Search checklist categories', 'orbis_checklist' ),
				'all_items'         => __( 'All checklist categories', 'orbis_checklist' ),
				'parent_item'       => __( 'Parent checklist category', 'orbis_checklist' ),
				'parent_item_colon' => __( 'Parent checklist category:', 'orbis_checklist' ),
				'edit_item'         => __( 'Edit checklist category', 'orbis_checklist' ),
				'update_item'       => __( 'Update checklist category', 'orbis_checklist' ),
				'add_new_item'      => __( 'Add new checklist category', 'orbis_checklist' ),
				'new_item_name'     => __( 'New checklist category name', 'orbis_checklist' ),
				'menu_name'         => __( 'Checklist categories', 'orbis_checklist' ),
			),
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => __( 'checklist-categories', 'orbis_checklist' ) ),
		)
	);
}

add_action( 'init', 'orbis_checklist_create_initial_post_types', 0 ); // highest priority
