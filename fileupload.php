<?php
/***
// Random code generator.
***/
function cvf_td_generate_random_code($length=10) {
   $string = '';
   $characters = "!@#$%^&*()_<>?23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
   for ($p = 0; $p < $length; $p++) {
      $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
   return $string;
}

/***
File Upload in Wordpres and attach it to post as post attachment
***/
// HTML Part
<input type = "file" name = "files[]" accept = ".pdf, .doc, .docx, .txt" class = "mng-files form-control" multiple />

// jQuery Part
$('submit_button').click(function(){
   var uploadfiles = $('.mng-files');
   var fd = new FormData();
   $.each($(uploadfiles), function(i, obj) {
      $.each(obj.files,function(j,file){
         fd.append('files[' + j + ']', file); //files[j] is name of input field
      })
   });
   fd.append('action', 'news');
   $.ajax({
			method: 'POST',
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			data: fd,
			processData: false,
    		contentType: false,
         .........
            .......
         success: function(s, textStatus, jqXHR){
            ......
         }
   });
});

// PHP part
add_action( 'wp_ajax_news', 'mnews_ajax' );
function mnews_ajax(){
	$newsName = $_POST['newsName'];
	$newsDesc = $_POST['newsDesc'];
	$newsDocs = $_POST['newsDocs'];
	$newsCatg = $_POST['newsCatg'];
// Filess
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

	if( defined('DOING_AJAX') && DOING_AJAX ){

		$args = array(
			'post_title' => wp_strip_all_tags( $newsName ),
			'post_type' => 'mnews',
			'post_status' => 'publish',
			'post_content' => $newsDesc
		);

		$newsid = wp_insert_post($args); // Add new policy
		wp_set_post_terms($newsid, $newsCatg, 'news_taxonomy'); // Add Category
				
	    // File upload handler
   
	    if( $_SERVER['REQUEST_METHOD'] == "POST" ){

	            foreach ( $_FILES['files']['name'] as $f => $name ) {
	                $extension = pathinfo( $name, PATHINFO_EXTENSION );
	               
	                if ( $_FILES['files']['error'][$f] == 4 ) {
	                    continue;
	                }

	                if ( $_FILES['files']['error'][$f] == 0 ) {
   
	                        // If no errors, upload the file...
	                        if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$name ) ) {
	                            $count++;
	                            $filename = $path.$name;
	                            $filetype = wp_check_filetype( basename( $filename ), null );
	                            $wp_upload_dir = wp_upload_dir();
	                            $attachment = array(
	                                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
	                                'post_mime_type' => $filetype['type'],
	                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	                                'post_content'   => '',
	                                'post_status'    => 'inherit'
	                            );
								 
	                            // Insert attachment to the database
	                            $attach_id = wp_insert_attachment( $attachment, $filename, $newsid );
	                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
	                           
	                            // Generate meta data
	                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	                            wp_update_attachment_metadata( $attach_id, $attach_data );
	                           $getattached[] = $wp_upload_dir['url'] . '/' . basename( $filename );
	                        }
	                }
	            } //End foreach
				$mnstr = json_encode($getattached);
				update_post_meta($newsid, 'attached_docs', $mnstr);
	    }

		die();
	} else {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}
}
