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
