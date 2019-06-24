<?php
namespace Optilab\TopList\PostTypes;

if (!function_exists('add_actiomn')) return false;

// Register Custom Post Type
function toplist_item_review_post_type() {

	$labels = array(
		'name'                  => _x( 'Toplist Item\'s Reviews', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'Toplist Item Review', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Toplist Item\'s Reviews', 'sage' ),
		'name_admin_bar'        => __( 'Toplist Items Reviews', 'sage' ),
		'archives'              => __( 'Review Archives', 'sage' ),
		'parent_item_colon'     => __( 'Parent Review:', 'sage' ),
		'all_items'             => __( 'All Reviews', 'sage' ),
		'add_new_item'          => __( 'Add New Review', 'sage' ),
		'add_new'               => __( 'Add New', 'sage' ),
		'new_item'              => __( 'New Review', 'sage' ),
		'edit_item'             => __( 'Edit Review', 'sage' ),
		'update_item'           => __( 'Update Review', 'sage' ),
		'view_item'             => __( 'View Review', 'sage' ),
		'search_items'          => __( 'Search Review', 'sage' ),
		'not_found'             => __( 'Not found', 'sage' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'sage' ),
		'featured_image'        => __( 'Featured Image', 'sage' ),
		'set_featured_image'    => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image'    => __( 'Use as featured image', 'sage' ),
		'insert_into_item'      => __( 'Insert into review', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this review', 'sage' ),
		'items_list'            => __( 'Reviews list', 'sage' ),
		'items_list_navigation' => __( 'Reviews list navigation', 'sage' ),
		'filter_items_list'     => __( 'Filter reviews list', 'sage' ),
	);
	$args = array(
		'label'                 => __( 'Toplist Item Review', 'sage' ),
		'description'           => __( 'Toplist Item\'s Reviews', 'sage' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'comments', 'revisions', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-admin-post',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => array('slug' => '', 'with_front' => false),
		'capability_type'       => 'post',
	);
	register_post_type( 'toplist_item_review', $args );

}
\add_action( 'init', __NAMESPACE__ . '\\toplist_item_review_post_type', 0 );
