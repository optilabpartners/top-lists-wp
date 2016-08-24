<?php
// Register Custom Post Type
function toplist_item_post_type() {

	$labels = array(
		'name'                  => _x( 'Toplist Items', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'Toplist Item', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Toplist Items', 'sage' ),
		'name_admin_bar'        => __( 'Toplist Items', 'sage' ),
		'archives'              => __( 'Item Archives', 'sage' ),
		'parent_item_colon'     => __( 'Parent Item:', 'sage' ),
		'all_items'             => __( 'All Items', 'sage' ),
		'add_new_item'          => __( 'Add New Item', 'sage' ),
		'add_new'               => __( 'Add New', 'sage' ),
		'new_item'              => __( 'New Item', 'sage' ),
		'edit_item'             => __( 'Edit Item', 'sage' ),
		'update_item'           => __( 'Update Item', 'sage' ),
		'view_item'             => __( 'View Item', 'sage' ),
		'search_items'          => __( 'Search Item', 'sage' ),
		'not_found'             => __( 'Not found', 'sage' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'sage' ),
		'featured_image'        => __( 'Featured Image', 'sage' ),
		'set_featured_image'    => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image'    => __( 'Use as featured image', 'sage' ),
		'insert_into_item'      => __( 'Insert into item', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'sage' ),
		'items_list'            => __( 'Items list', 'sage' ),
		'items_list_navigation' => __( 'Items list navigation', 'sage' ),
		'filter_items_list'     => __( 'Filter items list', 'sage' ),
	);
	$args = array(
		'label'                 => __( 'Toplist Item', 'sage' ),
		'description'           => __( 'Toplist Items', 'sage' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-feedback',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'rewrite'               => array('slug' => 'broker', 'with_front' => false),
		'capability_type'       => 'page',
	);
	register_post_type( 'toplist_item', $args );

}
add_action( 'init', 'toplist_item_post_type', 0 );


/**
* add order column to admin listing screen for header text
*/
function add_new_toplist_item_column($toplist_item_columns) {
  $toplist_item_columns['regulator'] = "Regulator";
  $toplist_item_columns['platform'] = "Platform";
  $toplist_item_columns['toplists'] = "Toplists";
  return $toplist_item_columns;
}
add_action('manage_edit-toplist_item_columns', 'add_new_toplist_item_column');

/**
* show custom order column values
*/
function show_order_column($name){
  global $post;

  switch ($name) {
    case 'toplists':
		$toplists_id = get_post_meta($post->ID, 'toplist_item_toplist');
		global $wpdb;
		$toplist_text = rtrim(implode(', ', $toplists_id), ', ');

		$toplists = $wpdb->get_results("SELECT name FROM {$wpdb->prefix}toplist WHERE id in ({$toplist_text})");
		$toplist_string = null;

		foreach ($toplists as $toplist) {
			$toplist_string .= "<em>{$toplist->name}</em>, ";
		}
		echo rtrim($toplist_string, ', ');
      break;
    case 'regulator':
      echo get_post_meta($post->ID, 'toplist_item_regulator', true);
      break;
    case 'platform':
      echo get_post_meta($post->ID, 'toplist_item_platform', true);
      break;
   default:
      break;
   }
}
add_action('manage_toplist_item_posts_custom_column','show_order_column');

/**
* make column sortable
*/
function order_column_register_sortable($columns){
  $columns['regulator'] = 'regulator';
  $columns['platform'] = 'platform';

  return $columns;
}
\add_filter('manage_edit-toplist_item_sortable_columns','order_column_register_sortable');