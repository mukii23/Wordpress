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

//#######################################################################################################
********************** ACTUAL CODE **************************
//#######################################################################################################
	
<?php get_header(); ?>

    <section class = "inner-page-wrapper">
        <section class = "container">
            <section class = "row content">
                <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <h1><?php the_title(); ?></h1>
                        <article class="entry-content">
                            <?php the_content(); ?>
                           
                            <div class = "col-md-6 upload-form">
                                <div class= "upload-response"></div>
                                <div class = "form-group">
                                    <label><?php __('Select Files:', 'cvf-upload'); ?></label>
                                    <input type = "file" name = "files[]" accept = "image/*" class = "files-data form-control" multiple />
                                </div>
                                <div class = "form-group">
                                    <input type = "submit" value = "Upload" class = "btn btn-primary btn-upload" />
                                </div>
                            </div>
                                                                                   
                            <script type = "text/javascript">
                            $(document).ready(function() {
                                // When the Upload button is clicked...
                                $('body').on('click', '.upload-form .btn-upload', function(e){
                                    e.preventDefault;

                                    var fd = new FormData();
                                    var files_data = $('.upload-form .files-data'); // The <input type="file" /> field
                                   
                                    // Loop through each data and create an array file[] containing our files data.
                                    $.each($(files_data), function(i, obj) {
                                        $.each(obj.files,function(j,file){
                                            fd.append('files[' + j + ']', file);
                                        })
                                    });
                                   
                                    // our AJAX identifier
                                    fd.append('action', 'cvf_upload_files');  
                                   
                                    // uncomment this code if you do not want to associate your uploads to the current page.
                                    fd.append('post_id', <?php echo $post->ID; ?>);

                                    $.ajax({
                                        type: 'POST',
                                        url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                                        data: fd,
                                        contentType: false,
                                        processData: false,
                                        success: function(response){
                                            $('.upload-response').html(response); // Append Server Response
                                        }
                                    });
                                });
                            });                    
                            </script>
                           
                           
                        </article>
                    </article>
                <?php endwhile; ?>
            </section>
        </section>
    </section>
   
<?php get_footer(); ?>

// ######SERVER SIDE...

add_action('wp_ajax_cvf_upload_files', 'cvf_upload_files');
add_action('wp_ajax_nopriv_cvf_upload_files', 'cvf_upload_files'); // Allow front-end submission

function cvf_upload_files(){
   
    $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg"); // Supported file types
    $max_file_size = 1024 * 500; // in kb
    $max_image_upload = 10; // Define how many images can be uploaded to the current post
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'post_parent'       => $parent_post_id,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
       
        // Check if user is trying to upload more than the allowed number of images for the current post
        if( ( count( $attachments ) + count( $_FILES['files']['name'] ) ) > $max_image_upload ) {
            $upload_message[] = "Sorry you can only upload " . $max_image_upload . " images for each Ad";
        } else {
           
            foreach ( $_FILES['files']['name'] as $f => $name ) {
                $extension = pathinfo( $name, PATHINFO_EXTENSION );
                // Generate a randon code for each file name
                $new_filename = cvf_td_generate_random_code( 20 )  . '.' . $extension;
               
                if ( $_FILES['files']['error'][$f] == 4 ) {
                    continue;
                }
               
                if ( $_FILES['files']['error'][$f] == 0 ) {
                    // Check if image size is larger than the allowed file size
                    if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                        $upload_message[] = "$name is too large!.";
                        continue;
                   
                    // Check if the file being uploaded is in the allowed file types
                    } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                        $upload_message[] = "$name is not a valid format";
                        continue;
                   
                    } else{
                        // If no errors, upload the file...
                        if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                           
                            $count++;

                            $filename = $path.$new_filename;
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
                            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                           
                            // Generate meta data
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                            wp_update_attachment_metadata( $attach_id, $attach_data );
                           
                        }
                    }
                }
            }
        }
    }
    // Loop through each error then output it to the screen
    if ( isset( $upload_message ) ) :
        foreach ( $upload_message as $msg ){       
            printf( __('<p class="bg-danger">%s</p>', 'wp-trade'), $msg );
        }
    endif;
   
    // If no error, show success message
    if( $count != 0 ){
        printf( __('<p class = "bg-success">%d files added successfully!</p>', 'wp-trade'), $count );  
    }
   
    exit();
}

// Random code generator used for file names.
function cvf_td_generate_random_code($length=10) {
 
   $string = '';
   $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
 
   for ($p = 0; $p < $length; $p++) {
       $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
 
   return $string;
 
}
