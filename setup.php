<?php

$toplist_basepath = '/mods/toplist/';

$toplist_includes = [
  'toplist-item-post-type.php',
  'toplist-item-review-post-type.php',
  'toplist-association-metabox.php',
  'toplistcontroller.php',
  'toplistmodel.php',
  'postfilter.php',
];

foreach ($toplist_includes as $file) {
  if (!$filepath = locate_template($toplist_basepath . $file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

use Optilab\Mods\TopList;
use Roots\Sage\DB;
use Roots\Sage\Assets;

\add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

function setup() {
	TopList\TopListController::bootstrap();
}


\add_action( 'admin_menu', function() use ($toplist_basepath){
	add_menu_page( 
		'TopLists',
		'TopLists',
		'publish_posts',
		'toplists',
		 function() use ($toplist_basepath) {
			require_once(TEMPLATEPATH  . $toplist_basepath . '/templates/admin-toplist.template.php');
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
	wp_enqueue_script('toplist/admin', Assets\asset_path('scripts/admin.js'), ['jquery', 'backbone'], null, true);


} );


\add_action( 'wp_ajax_toplist', [__NAMESPACE__ . '\\TopListRequestHandler','topList'] );
\add_action( 'wp_ajax_toplists', [__NAMESPACE__ . '\\TopListRequestHandler','toplists'] );
\add_action( 'wp_ajax_toplist_items', [__NAMESPACE__ . '\\TopListRequestHandler','toplist_items'] );


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

	public static function toplist()
	{

		$toplist = json_decode( file_get_contents( "php://input" ) );

		$method = self::method_identifier();

		switch ($method) {
			case 'DELETE':
				echo TopList\TopListController::deleteOne(
					new TopList\TopListModel(
						[ 'id' => $_SERVER['HTTP_ID'] ]
				));
				wp_die();
				break;

			case 'POST':
				$toplist = TopList\TopListController::create(
					new TopList\TopListModel(
						[ 'name' => $toplist->name, 'description' => $toplist->description ]
				));
				if ($toplist instanceof TopList\TopListModel) {
					echo json_encode($toplist);
				}
				wp_die();
				break;

			case 'PUT':

				$toplist = TopList\TopListController::updateOne(
					new TopList\TopListModel(
						[ 'id' => $toplist->id, 'name' => $toplist->name, 'description' => $toplist->description ]
				));
				if ($toplist instanceof TopList\TopListModel) {
					echo json_encode($toplist);
				}
				wp_die();
				break;
			
			case 'GET':
				$toplist = TopList\TopListController::fetchOne(
					new TopList\TopListModel(
						[ 'id' => $toplist->name ]
				));
				if ($toplist instanceof TopList\TopListModel) {
					echo json_encode($toplist);
				}
				wp_die();
				break;
		}
		
	}

	public static function toplists()
	{

		$method = self::method_identifier();

		$toplists = TopList\TopListController::fetchMany();	
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
				    'order'               => 'ASC',
				    'orderby'             => 'menu_order date',
				    'ignore_sticky_posts' => false,

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

				$query = new \WP_Query( $args );
				if ($query->have_posts())
    				echo json_encode($query->posts);
				break;
			
		}
		wp_die();

	}
}

// add
// TopList\TopListController::create(
// 	new TopList\TopListModel(
// 		[ 'name' => "Name 1", 'description' => "Lorem Ipsum" ]
// ));

// //FetchOne
// TopList\TopListController::fetchOne(
// 	new TopList\TopListModel(
// 		[ 'id' => 1, 'name' => "Name 2", 'description' => "Lorem Ipsum" ]
// ));

//FetchMany
// TopList\TopListController::fetchMany(
// 	[ 'description' => "Lorem Ipsum" ], 
// 	true
// );

// //UpdateOne
// TopList\TopListController::updateOne(
// 	new TopList\TopListModel(
// 		[ 'id' => 1, 'name' => "Name 2", 'description' => "Lorem Ipsum" ]
// ));

//DeleteOne
// TopList\TopListController::deleteOne(
// 	new TopList\TopListModel(
// 		[ 'id' => 1, 'name' => "Name 2", 'description' => "Lorem Ipsum" ]
// ));





// exit;