<?php
namespace Optilab\TopList\MetaBoxes;

use Optilab\WPMetaBoxBuilder;

/**
* Implement CodeBox class for the post/page that uses front-page.php template file.
*/
class TopListReviewMetaBoxBootstrap extends WPMetaBoxBuilder\Bootstrap
{

  public function __construct()
  {
    add_action( 'add_meta_boxes_toplist_item_review',  array( $this, 'register' ) );
    add_action( 'save_post',  array( $this, 'save' ) );
  }

  /**
   * Inintite MetaBox class and setup the metaboxes to be shown in the admin page
   **/
  public function register($post)
  {
    $setup = new WPMetaBoxBuilder\MetaBox($post);

    // top section
    $setup->add_meta_box(
      'toplist_item_4review_codebox',
      'Toplist Item',
      function($post) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'toplist_item_4review_codebox', 'toplist_new_nonce' );
        $toplist_item_4review = get_post_meta($post->ID, 'toplist_item_4review', true);

        $posts = \get_posts('post_type=toplist_item&status=published&posts_per_page=-1&ignore_sticky_posts=false&orderby=name&order=ASC');
        if (count($posts)):
          ?><select required name="toplist_item_4review" id="toplist_item_4review" style="width:100%" aria-required="true" ><option value="0">Select Toplist Item</option><?php
          foreach ( $posts as $post ) : ?>
            <option
            value="<?php echo $post->ID;?>"
            <?php if($post->ID == $toplist_item_4review) echo 'selected'; ?>
            ><?php echo $post->post_title; ?></option>
          <?php endforeach;
          ?></select><?php
        endif;
        wp_reset_postdata();
        ?>
      <?php
      },
      $post->post_type , 'side', 'low'
    );

    $setup->init(function() { return true; });
  }

  /**
   * Save post handler
   */
  function save($post_id) {

    parent::save($post_id);

    if (  !isset( $_POST['toplist_item_4review'] ) ) {
      return;
    }

    update_post_meta($post_id, 'toplist_item_4review', $_POST['toplist_item_4review']);

  }

}
