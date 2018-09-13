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

