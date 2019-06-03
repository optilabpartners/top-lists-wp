<?php
namespace Optilab\TopList;

use Optilab\DB;
use App;
global $post;

$toplist_basepath = dirname(__FILE__);

\add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

function setup() {
    Controllers\TopListController::bootstrap();
    MetaBoxes\TopListMetaBoxBootstrap::init();
    MetaBoxes\TopListReviewMetaBoxBootstrap::init();
}


\add_action( 'admin_menu', function() use ($toplist_basepath){
    add_menu_page(
        'TopLists',
        'TopLists',
        'publish_posts',
        'toplists',
         function() use ($toplist_basepath) {
            require_once( $toplist_basepath . '/resources/templates/admin-toplist.template.php');
        },
        'dashicons-chart-pie',
        25
    );
});

\add_action( 'admin_enqueue_scripts', function($hook) {
    if ( 'toplevel_page_toplists' != $hook ) {
        return;
    }

    wp_enqueue_style('toplist/bootstrapcss', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
    wp_enqueue_script('toplist/bootstrapjs', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', ['jquery'], null, true);
    
    if (function_exists('\\App\\asset_path')) {
        wp_enqueue_style('toplist/admincss', \App\asset_path('styles/admin.css'), array('toplist/bootstrapcss'), null);
        wp_enqueue_script('toplist/admin', \App\asset_path('scripts/toplist-admin.js'), ['jquery', 'backbone'], null, true);
    } else {
        wp_enqueue_style('toplist/admincss', get_template_directory_uri() . '/../dist/styles/admin.css', array(), null);
        wp_enqueue_script('toplist/admin', get_template_directory_uri() . '/../dist/scripts/toplist-admin.js', ['jquery', 'backbone'], null, true);
    }
    
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-mouse' );
    wp_enqueue_script( 'jquery-ui-sortable' );

} );


\add_action( 'wp_ajax_toplist', [__NAMESPACE__ . '\\RequestHandlers\\TopListRequestHandler','toplist'] );
\add_action( 'wp_ajax_toplists', [__NAMESPACE__ . '\\RequestHandlers\\TopListRequestHandler','toplists'] );
\add_action( 'wp_ajax_toplist_item', [__NAMESPACE__ . '\\RequestHandlers\\TopListRequestHandler','toplist_item'] );
\add_action( 'wp_ajax_toplist_items', [__NAMESPACE__ . '\\RequestHandlers\\TopListRequestHandler','toplist_items'] );



add_filter('query_vars', __NAMESPACE__ . '\\add_state_var', 0, 1);
function add_state_var($vars){
    $vars[] = 'toplist_type';
    return $vars;
}


add_action( 'init', __NAMESPACE__ . '\\add_rewrite_rules' );
function add_rewrite_rules() {

    $arg = array(
        'post_type' => 'toplist_item_review',
        'no_conflict' => '1',
        'posts_per_page' => '-1',
        'status' => 'publish'
    );

    $toplist_item_reviews= new \WP_Query($arg);

    while($toplist_item_reviews->have_posts() ) : $toplist_item_reviews->the_post();

        global $post;
        $toplist_id = get_post_meta($post->ID, 'toplist_item_4review', true);
        $types = get_the_terms($toplist_id, 'toplist_type' );
        if($types != false) {
            add_rewrite_rule(  $types[0]->slug . '/' . $post->post_name . '/?$', 'index.php?post_type=toplist_item_review&name=' . $post->post_name, 'top');
        } else {
            add_rewrite_rule(  '/' . $post->post_name . '/?$', 'index.php?post_type=toplist_item_review&name=' . $post->post_name, 'top');
        }
            

    endwhile;
}


add_action('delete_post',  __NAMESPACE__ . '\\flush_project_links', 99, 2);
add_action('save_post',  __NAMESPACE__ . '\\flush_project_links', 99, 2);
function flush_project_links( $post_id) {

   if ( get_post_type( $post_id ) != 'toplist_item_review' )
        return;

   add_rewrite_rules();
   flush_rewrite_rules();

}

add_filter( 'post_type_link', __NAMESPACE__ . '\\custom_permalinks', 10, 2 );
function custom_permalinks( $permalink, $post ) {
    if ( $post->post_type != 'toplist_item_review' )
        return $permalink;

    $toplist_id = get_post_meta($post->ID, 'toplist_item_4review', true);
    $types = get_the_terms($toplist_id, 'toplist_type' );
    $new_permalink = str_replace("toplist_item_review", $types[0]->slug, $permalink);

    return $new_permalink;
}

add_action( 'wp_insert_post', function($post_id) use ($post) {
    // If this is just a revision, exit away.
    if ( wp_is_post_revision( $post_id ) && $post->post_type !=  'toplist_item_review')
        return;
    flush_rewrite_rules();
} );

\add_action('admin_print_scripts', function() {
    ?>
    <script language="javascript" type="text/javascript">

        jQuery(document).ready(function() {

            jQuery('#post').submit(function() {

              if(jQuery('#toplist_item_4review').val() == 0) {
                alert('Select a Toplist Item');
                return false;
              }
            });
        });
    </script>
    <?php
},99);
