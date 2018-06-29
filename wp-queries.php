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
        
        
    /***********************************************************
     ****Add Footer Section Settings in  Theme Customizer*******
     ***********************************************************/
    
    function add_custom_settings($wp_customize){
        
        $wp_customize->add_section( 'footer_section' , array(
            'title'      => 'Footer Section',
            'priority'   => 60,
        ) );
        $wp_customize->add_setting( 'footer_text' , array(
            
//            'default' => 'Copyright © 2018  NewsClues. All Rights Reserved.',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_text', array(
            'type' => 'textarea',
            'label'        => 'Input text for footer here.',
            'section'    => 'footer_section',
            'settings'   => 'footer_text',
        ) ) );
        
        
    }
    
    add_action( 'customize_register', array($this, 'add_custom_settings' ) );


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
