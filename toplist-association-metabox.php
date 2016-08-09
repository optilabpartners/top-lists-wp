<?php
namespace OptiLab\Mods\TopList;
use Optilab\Mods\WP_MetaBox\TemplateMetaBox;
use Optilab\Mods\WP_MetaBox\CodeBox;

/**
* Implement CodeBox class for the post/page that uses front-page.php template file.
*/
class TopListAssociationMetaBox extends CodeBox\CodeBox
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
  
    $setup = new TemplateMetaBox\TemplateMetaBox($post);

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
          <select multiple name="toplist_item_toplist[]" id="toplist_item_toplist" aria-required="true" style="width: 100%;" >
            <option value="0">Select TopList</option>
            <?php if ( count($results) > 0 ): ?>
                <?php foreach ( $results as $result ): ?>
                    <?php if ( in_array($result->id, $toplist_item_toplist)  ) : ?>
            <option value="<?= $result->id ?>" selected ><?= $result->name ?></option>
                    <?php else: ?>
            <option value="<?= $result->id ?>"><?= $result->name ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
          </select>
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


    if ( ! isset( $_POST['toplist_item_toplist'] ) ) {
      return;
    }
    // add more if needed
    $toplists = $_POST['toplist_item_toplist'];

    delete_post_meta($post_id, 'toplist_item_toplist');

    foreach ($toplists as $toplist) {
      add_post_meta($post_id, 'toplist_item_toplist', $toplist);
      if (!get_post_meta( $post_id, 'toplist_item_toplist_' . $toplist . '_rank', true )) {
        add_post_meta($post_id, 'toplist_item_toplist_' . $toplist . '_rank', 1);
      }
    }

    
  }

}
//Initiate the class
TopListAssociationMetaBox::init();