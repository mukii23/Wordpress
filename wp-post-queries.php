 /*****************************
  ****Change POST Permalink
  *****************************/
    
    function append_slug($data) {
        global $post_ID;

        if (!empty($data['post_name']) && $data['post_status'] == "publish" && $data['post_type'] == "post") {

                if( !is_numeric(substr($data['post_name'], -4)) ) {
                    $random = rand(1111,9999);
                    $data['post_name'] = sanitize_title('forum_news', $post_ID);
                    $data['post_name'] .= '-' . $random;
                }

        }
         return $data; 
         
    }
    
    add_filter('wp_insert_post_data', 'append_slug', 10);


/*****************************
  ****Change POST 'slug' name from URL
*****************************/
    

  function remove_slug( $post_link, $post, $leavename ) {

      if ( 'mukii' != $post->post_type || 'publish' != $post->post_status ) {
        return $post_link;
      }

      $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

      return $post_link;
  }
  add_filter( 'post_type_link', 'remove_slug', 10, 3 );
  function process_request( $query ) {

      if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
      }

      if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'page', 'mukii' ) );
      }
  }
  add_action( 'pre_get_posts', 'process_request' );


/*****************************
  ****Remove custom post-type slug from custom post url
*****************************/

function gp_remove_cpt_slug( $post_link, $post ) {
    if ( ('research' === $post->post_type && 'publish' === $post->post_status) || ('rpgmc_event' === $post->post_type && 'publish' === $post->post_status) ) {
        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    }
    return $post_link;
}
add_filter( 'post_type_link', 'gp_remove_cpt_slug', 10, 2 );

function gp_add_cpt_post_names_to_main_query( $query ) {
   // Bail if this is not the main query.
   if ( ! $query->is_main_query() ) {
    return;
   }
   // Bail if this query doesn't match our very specific rewrite rule.
   if ( ! isset( $query->query['page'] ) || 2 !== count( $query->query ) ) {
    return;
   }
   // Bail if we're not querying based on the post name.
   if ( empty( $query->query['name'] ) ) {
    return;
   }
   // Add CPT to the list of post types WP will include when it queries based on the post name.
   $query->set( 'post_type', array( 'post', 'page', 'research', 'rpgmc_event' ) );
}
add_action( 'pre_get_posts', 'gp_add_cpt_post_names_to_main_query' );

/*****************************
  ****Display Publish Posts based on 'Latest', 'Daily', 'Weekly'
*****************************/
    
    switch($radiovalue){
            case 'recent':
                $args = array('posts_per_page' => $postcount, 'offset' => $offset, 'post_status' => 'publish');
                break;
            case 'daily':
                $args = array('posts_per_page' => $postcount, 'offset' => $offset,
                               'post_status' => 'publish',
                         /**QUERY FOR ONE WEEK***/
                               'date_query' => array(
                                  'year' => date( 'Y', current_time( 'timestamp' ) ),
                                   'month' => date( 'n', current_time( 'timestamp' ) ),
                                   'day' => date( 'j', current_time( 'timestamp' ) )
                                  )
                         /**QUERY FOR LAST 7 DAYS***/
                               'date_query' => array(
                                   'after' => '1 week ago',
                              /***OR***/
                                   'after' => date('Y-m-d', strtotime('-7 days')),
                                    )
                              );
                break;
            case 'weekly':
                $args = array('posts_per_page' => $postcount, 'offset' => $offset, 
                               'post_status' => 'publish',
                               'date_query' => array(
                                      'year' => date( 'Y' ),
                                      'week' => date( 'W' ),
                                  )
                              );
                break;
            default:
                $args = array('posts_per_page' => $postcount, 'offset' => $offset);
                break;
        }
      

    /***######QUERY For display the last 24 hours POSTS#####***/

     'date_query' => array(
                             'after' => '24 hours ago'
                             )
        
        

 /*****************************
  **** POST Pagination
  *****************************/

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'paged' => $paged,
);

$query_result = new WP_Query($args);

<?php 
        echo paginate_links( array(
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'total'        => $query_result->max_num_pages,
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'format'       => '?paged=%#%',
            'show_all'     => false,
            'type'         => 'plain',
            'end_size'     => 2,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => sprintf( '<i></i> %1$s', __( '<< First', 'text-domain' ) ),
            'next_text'    => sprintf( '%1$s <i></i>', __( 'Last >>', 'text-domain' ) ),
            'add_args'     => false,
            'add_fragment' => '',
        ) );
    ?>
