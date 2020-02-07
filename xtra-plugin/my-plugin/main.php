<?php

/*
Plugin Name: News Plugin
Author: Mukesh Kumar
Description: Display latest news. Use shortcode '[latest_news]'.

*/

/***********
== Create custom post-type 'latest_news'
************/

function news_post_type(){
    $args = array(
        'label'             => 'Our News',
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'news' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
		'menu_icon'			 => 'dashicons-megaphone',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes', 'custom-fields' ),
    );
	register_post_type( 'latest_news', $args );
}
add_action( 'init', 'news_post_type' );

register_activation_hook( __FILE__, 'my_rewrite_flush' );
function my_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    news_post_type();
 
    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}


/***********
== Fetch posts as shortcode
************/
function latest_news_display(){
$array = array(
		'post_type' => 'latest_news',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'order' => 'ASC'
	);	
	$query = new WP_Query($array);
	$count = 1;
	
	if ( $query->have_posts() ):
	?>
	<div classs="newsDiv">
		<?php 
		while($query->have_posts()):
			$query->the_post();
	
			$news_link = get_post_meta( get_the_ID(), 'cutom_link', true );
			$publ_in = get_post_meta( get_the_ID(), 'published_in', true );
			$news_link = ($news_link)?$news_link:get_the_permalink();
			$publ_in = ($publ_in)?'<i>('. $publ_in .')</i>':'';
	
			echo '<p>' . $count++ . '. <a href="'. $news_link .'" target="_blank"><strong>' . get_the_title() . '</strong></a>'. $publ_in .'</p>';
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<?php
	else:
		echo '<h3>Sorry, but there are no updated news here. Contact to particular authority for more news. Thanks.</h3>';
	endif;
	
}
add_shortcode( 'latest_news', 'latest_news_display' );