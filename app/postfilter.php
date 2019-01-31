<?php
namespace Optilab\Mods\TopList;

function add_toplist_filter_manage_toplist_items(){

    //execute only on the 'post' content type
    global $post_type;
    if($post_type !== 'toplist_item')
    	return false;

    global $wpdb;
    $toplists = $wpdb->get_results( "SELECT id, name from {$wpdb->prefix}toplist ORDER BY name" );

    ?>
    <label for="toplist" class="screen-reader-text">toplists</label>
    <select name="toplist">
    <option value=""><?php _e('Filter by TopLists', 'sage'); ?></option>
    <?php
        $current_v = isset($_GET['toplist'])? $_GET['toplist']:'';
        foreach ($toplists as $toplist) {
            printf
                (
                    '<option value="%s"%s>%s</option>',
                    $toplist->id,
                    $toplist->id == $current_v? ' selected':'',
                    $toplist->name
                );
            }
    ?>
    </select>
    <?php

}
add_action('restrict_manage_posts', __NAMESPACE__ . '\\add_toplist_filter_manage_toplist_items');


add_filter( 'parse_query',  function( $query ){
    global $pagenow, $post_type;
    if( $post_type !== 'toplist_item') {
        return $query;
    }
    if (!isset($_GET['post_type'])) {
        return $query;
    }
    else if ( $_GET['post_type'] == $post_type && is_admin() && $pagenow=='edit.php' && isset($_GET['toplist']) && $_GET['toplist'] != '') {
        $query->query_vars['meta_key'] = 'toplist_item_toplist';
        $query->query_vars['meta_value'] = $_GET['toplist'];
        return $query;
    }
});

