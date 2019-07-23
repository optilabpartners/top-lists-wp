<?php

namespace Optilab\TopList\RequestHandlers;
use Optilab\TopList\Controllers;
use Optilab\TopList\Models;

/**
* Toplist request handler
*/
class TopListRequestHandler
{

    private static function method_identifier() {
        $method = '';

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'delete'))  $method = "DELETE";
        if ($_SERVER['REQUEST_METHOD'] === 'PUT' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'put'))  $method = 'PUT';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'post'))  $method = 'POST';
        if ($_SERVER['REQUEST_METHOD'] === 'GET' || (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'get'))  $method = 'GET';

        return $method;
    }

    public static function toplist() {

        $toplist = json_decode( file_get_contents( "php://input" ) );

        $method = self::method_identifier();

        switch ($method) {
            case 'DELETE':
                echo Controllers\TopListController::deleteOne(
                    new Models\TopListModel(
                        [ 'id' => $_SERVER['HTTP_ID'] ]
                ));
                wp_die();
                break;

            case 'POST':
                $toplist = Controllers\TopListController::create(
                    new Models\TopListModel(
                        [ 'name' => $toplist->name, 'description' => $toplist->description ]
                ));
                if ($toplist instanceof Models\TopListModel) {
                    echo json_encode($toplist);
                }
                wp_die();
                break;

            case 'PUT':

                $toplist = Controllers\TopListController::updateOne(
                    new Models\TopListModel(
                        [ 'id' => $toplist->id, 'name' => $toplist->name, 'description' => $toplist->description ]
                ));
                if ($toplist instanceof Models\TopListModel) {
                    echo json_encode($toplist);
                }
                wp_die();
                break;

            case 'GET':
                $toplist = Controllers\TopListController::fetchOne(
                    new Models\TopListModel(
                        [ 'id' => $toplist->name ]
                ));
                if ($toplist instanceof Models\TopListModel) {
                    echo json_encode($toplist);
                }
                wp_die();
                break;
        }

    }

    public static function toplists() {

        $method = self::method_identifier();

        $toplists = Controllers\TopListController::fetchMany();

        echo json_encode($toplists);
        wp_die();

    }

    public static function toplist_items() {
        $method = self::method_identifier();
        switch ($method) {
            case 'GET':
                $toplist_id = (int)$_SERVER['HTTP_TOPLISTID'];

                $args = array(
                    //Type & Status Parameters
                    'post_type'   => 'toplist_item',
                    'post_status' => 'publish',

                    //Order & Orderby Parameters
                    'order'                 => 'ASC',
                    'orderby'               => 'meta_value_num',
                    'meta_key'              => 'toplist_item_toplist_' . $toplist_id . '_rank',
                    'ignore_sticky_posts'   => false,

                    //Pagination Parameters
                    'posts_per_page'      => -1,

                    'meta_query'          => array(
                      array(
                        'key'     => 'toplist_item_toplist',
                        'value'   => $toplist_id,
                        'compare' => '=',
                      ),
                    ),
                );

                \add_filter( 'posts_results', function($posts) use ($toplist_id) {

                    if (count($posts) > 0 ) {

                        foreach ($posts as &$post) {
                            if ($post->post_type = "toplist_item") {
                                $rank = get_post_meta($post->ID, 'toplist_item_toplist_' . $toplist_id . '_rank', true);
                                $post->rank = ($rank != "")? (int)$rank: 0 ;
                                $post->toplist = $toplist_id;
                                apply_filters('admin_toplist_toplist_items', $post);
                            }
                        }
                    }
                    return $posts;
                } );

                $query = new \WP_Query( $args );
                if ($query->have_posts())
                    echo json_encode($query->posts);
            break;
            case 'PUT':
                $toplist_items = json_decode(file_get_contents("php://input"));
                if ($toplist_items) {
                    foreach ($toplist_items as $toplist_item) {
                        delete_post_meta( $toplist_item->ID, 'toplist_item_toplist_' . $toplist_item->toplist . '_rank' );
                        $update = add_post_meta( $toplist_item->ID, 'toplist_item_toplist_' . $toplist_item->toplist . '_rank', $toplist_item->rank, true );
                    }
                }
            break;
        }
        wp_die();

    }

    public static function toplist_item() {
        $toplist = json_decode( file_get_contents( "php://input" ) );
        $method = self::method_identifier();

        wp_die();
        switch ($method) {
            case 'PUT':
            break;
        }

    }
}
