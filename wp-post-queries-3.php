/****************
*** Count from WP Query
*****************/

function get_post_count(){
	$array = array(
				'post_type' => 'my_post',
				'post_status' => 'publish,
				'posts_per_page' => 5000,
				);
	$query_result = new WP_Query($array);	
	return ($query_result->post_count) ? $query_result->post_count : '-';
	
}


/****************
*** Get post views
*****************/
function getViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}
function setViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

Aftert this setup in functions.php add 'setViews(get_the_ID())' in single file and set 'getViews(get_the_ID())' where you want to view the post views count.
