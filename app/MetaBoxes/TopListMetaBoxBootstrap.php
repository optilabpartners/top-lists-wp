<?php
namespace Optilab\TopList\MetaBoxes;

use Optilab\WPMetaBoxBuilder;

/**
* Implement CodeBox class for the post/page that uses front-page.php template file.
*/
class TopListMetaBoxBootstrap extends WPMetaBoxBuilder\Bootstrap
{

  public function __construct()
  {
    add_action( 'add_meta_boxes_toplist_item',  array( $this, 'register' ) );
    add_action( 'save_post',  array( $this, 'save' ) );
  }

  /**
   * Inintite TemplateMetaBox class and setup the metaboxes to be shown in the admin page
   **/
  public function register($post)
  {

    $setup = new WPMetaBoxBuilder\MetaBox($post);

    $setup->add_meta_box(
      'toplist_item_toplist_codebox',
      'TopList',
      function($post) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'toplist_item_toplist_codebox', 'fxexplained_new_nonce' );
        $toplist_item_toplist = get_post_meta($post->ID, 'toplist_item_toplist');

        global $wpdb;
        $results = $wpdb->get_results( "SELECT id, name from {$wpdb->prefix}toplist ORDER BY name" );
        ?>
        <div class="form-field">
        <?php if ( count($results) > 0 ): ?>
          <?php foreach ( $results as $result ): ?>
            <input type="checkbox" name="toplist_item_toplist[]" id="toplist_item_toplist" value="<?= $result->id ?>" <?= ( in_array($result->id, $toplist_item_toplist)  )? 'checked': ''; ?> > <?= $result->name ?><br>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php
      },
      $post->post_type, 'side', 'low'
    );


    $setup->init(function() {
      return true;
    });
  }

  /**
   * Save post handler
   */
  function save($post_id) {

    parent::save($post_id);

    // add more if needed
    $toplists = $_POST['toplist_item_toplist'];
    /* AJAX check  */
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      /* special ajax here */
      return;
    }
    delete_post_meta($post_id, 'toplist_item_toplist');

    if(!empty($toplists)) {
      foreach ($toplists as $toplist) {
        add_post_meta($post_id, 'toplist_item_toplist', $toplist);
        if (!get_post_meta( $post_id, 'toplist_item_toplist_' . $toplist . '_rank', true )) {
          global $wpdb;
          $last_rank = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key='toplist_item_toplist_{$toplist}_rank' ORDER BY meta_value DESC LIMIT 1" );
          add_post_meta($post_id, 'toplist_item_toplist_' . $toplist . '_rank', $last_rank+1);
        }
      }
    }


  }

}
//Initiate the class
