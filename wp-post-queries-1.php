/************************
******* Display Multiple Categories in Post.
************************/

$terms = get_the_category();
$post_category = array();
foreach($terms as $post_name){
    $post_category[] = $post_name->name;
    
    /****FOR CATEGORY LINK****/
    
    $link = get_category_link($c_name->cat_ID);
}
echo implode(', ', $post_category);

/************************
******* Create Featured Post using Custom Meta-Box.
************************/

add_action('add_meta_boxes', array($this, 'featured_meta_box'));
add_action('save_post', array($this, 'sm_meta_save'));

function featured_meta_box(){
    add_meta_box( 'featured_meta', __( 'Featured Post' ), array($this, 'featured_meta_callback'), 'post', 'side' );
}
function featured_meta_callback($post){
    $featured = get_post_meta( $post->ID );
    $result = get_option('featured_post');
       ?>
    <p>
       <div class="sm-row-content">
          <label for="meta-checkbox">
                <input type="checkbox" name="meta-checkbox" id="meta-checkbox" value="yes" <?php if ( $result == $post->ID ) {echo 'checked="checked"';} ?> />
                <?php _e( 'Make this post featured.' )?>
          </label>
       </div>
</p>

    <?php
}
        
//****Save featured post.
        
function sm_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'sm_nonce' ] ) && wp_verify_nonce( $_POST[ 'sm_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
          return;
    }

    // Checks for input and saves
    if( isset( $_POST[ 'meta-checkbox' ] ) ) {
        update_option( 'featured_post', $post_id, true );
    } else {
       delete_option( 'featured_post' );
    }
}
