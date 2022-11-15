<?php
/******************************************
==========Contact form-7 before send email hook=============
*******************************************/
add_action( 'wpcf7_before_send_mail', 'cspd_call_after_for_submit' );
function cspd_call_after_for_submit( $WPCF7_ContactForm ){
    $currentformInstance  = WPCF7_ContactForm::get_current();
    $contactformsubmition = WPCF7_Submission::get_instance();
	
    if($contactformsubmition){
    	$posted_data = $contactformsubmition->get_posted_data(); 
    }

// 	$WPCF7_ContactForm->skip_mail = true;
	  if($currentformInstance->id == 43){
      $userdata1 = array(
			'custom attribute name' =>  $posted_data['input field name']);
    }
}

/********************************************
======== RESET Password Shortcode =========
***********************************************/

// RESET Password Shortcode
add_shortcode('exl_lost_password','exl_lostpassword');
function exl_lostpassword(){
	?>
<div id="exlostpassword">
	<div class='inputtext'><input type="email" class="emailinput" value="" />
		<div class="error"></div></div>
	<div class="inputsubmit">
		<input type="button" class="emailsubmit" value="Reset Password" /></div>
	<div class="emailsuccess"></div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#exlostpassword .emailsubmit').on('click',function(){
			var inputmail = jQuery('#exlostpassword .emailinput').val();
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'exlemailconfirm',
					'emailvalue' : inputmail
				},
				success: function(s){
					var result = JSON.parse(s);
					jQuery('#exlostpassword .error').html(result.error);
					jQuery('#exlostpassword .emailsuccess').html(result.success);
					jQuery('#exlostpassword .emailinput').val('');
				},
				error: function(e){
					console.log(e.error);
				}
			});
		});
	});
</script>
	<?php
}
add_action('wp_ajax_exlemailconfirm','emailajax');
add_action('wp_ajax_nopriv_exlemailconfirm','emailajax');
function emailajax(){
	global $wpdb;
	$emailvalue = $_POST['emailvalue'];
	$fetchuser = $wpdb->get_results("SELECT ID, user_pass FROM wpie_users WHERE user_email = '".$emailvalue."'");
	if(count($fetchuser) == 1){
		
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		$finalpassword = implode($pass);
		wp_set_password( $finalpassword, $fetchuser[0]->ID );
		
		//Email module
		$subject = "Password reset email from EXLAnalytics.";
		$message = "Please use this password for login : ".$finalpassword;
// 		$message .= "Username: ".$emailvalue."\r\n";
// 		$message .= "Password: ".$finalpassword."\r\n";
		$headers = array("Content-Type: text/html; charset=UTF-8");
		wp_mail($emailvalue, $subject, $message, $headers);
		
		echo json_encode(array(
			'success' => 'Please check your email for password.'
		));
	}else{
		echo json_encode(array(
			'error' => 'Entered email doesn\'t exist'
		));
	}
	die();
}

/********************************************
======== FILE Upload shortcode =========
***********************************************/

// FILE Upload system
add_shortcode('exl-file-upload', 'exl_file_upload_system');
function exl_file_upload_system(){
	?>
<div id="exlfileupload">
	<div class="xfile">
		<input type="file" name="exlfile" class="exlfile" accept=".doc,.docx,.pdf,image/*" /></div>
	<div class="filesubmit">
		<input type="button" class="exlfilesubmit" value="Submit File" /></div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(document).find('#exlfileupload .exlfilesubmit').on('click',function(){
			var uploadfiles = jQuery(document).find('#exlfileupload .exlfile').prop('files')[0];
		   var fd = new FormData();

			console.log(uploadfiles);
		   fd.append('action', 'exlupload');
			fd.append('exlfile', uploadfiles);
		   jQuery.ajax({
					type: 'POST',
					url: 'domain-name/wp-admin/admin-ajax.php',
					data: fd,
					processData: false,
					contentType: false,
				 success: function(s, textStatus, jqXHR){}
		   });
		});
	});
</script>
	<?php
	
}
add_action('wp_ajax_nopriv_exlupload','exlfileupload_ajax_call');
add_action('wp_ajax_exlupload','exlfileupload_ajax_call');
function exlfileupload_ajax_call(){

    // Image upload handler
  
	$file_name = $_FILES['exlfile']['name'];
    $file_temp = $_FILES['exlfile']['tmp_name'];

    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents( $file_temp );
    $filename = basename( $file_name );
    $filetype = wp_check_filetype($file_name);
    $filename = time().'.'.$filetype['ext'];

    if ( wp_mkdir_p( $upload_dir['path'] ) ) {
    	$file = $upload_dir['path'] . '/' . $filename;
    }
    else {
    	$file = $upload_dir['basedir'] . '/' . $filename;
    }

    file_put_contents( $file, $image_data );
    $wp_filetype = wp_check_filetype( $filename, null );
    $attachment = array(
                  'post_mime_type' => $wp_filetype['type'],
                  'post_title' => sanitize_file_name( $filename ),
                  'post_content' => '',
                  'post_status' => 'inherit'
   	);

    $attach_id = wp_insert_attachment( $attachment, $file );
	  require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
	
	die();
}

/********************************************
======== Custom option for users menu =========
***********************************************/
// Backend Option for Users List
add_action( 'admin_menu', 'backend_userlistoption' );
function backend_userlistoption(){
	add_submenu_page( 
            'users.php',
            __( 'Exl Users', 'exl-analytics' ),
            __( 'Exl Users', 'exl-analytics' ),
            'administrator',
            'exl-user-list',
            'backend_userlistdata' );
}
function backend_userlistdata(){
  //Include these files for datatable connection
    wp_enqueue_script('exl_datatableapi_js','https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js',array('jquery'));
		wp_enqueue_script('exl_datatable_js','https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js',array('jquery'));
		wp_enqueue_style('exl_datatable_css','https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css',true);
		wp_enqueue_script('exl_datatableselect_js','https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js',array('jquery'));
		wp_enqueue_script('exl_datatablebtn_js','https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',array('jquery'));
		wp_enqueue_script('exl_datatablehtml_js','https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',array('jquery'));
		wp_enqueue_script('exl_datatableprint_js','https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',array('jquery'));
		wp_enqueue_script('exl_datatablehtml5_js','https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js',array('jquery'));
		wp_enqueue_style('exl_datatablebtn_css','https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css',true);
?>
  <script type="text/javascript">
        
            jQuery(document).ready(function(){
				
               jQuery('#jodatatable').DataTable({
				   dom: 'Blfrtip',
				   buttons: [
// 							   {  
// 								  extend: 'copy'
// 							   },
// 							   {
// 								  extend: 'pdf',
// 								  exportOptions: {
// 									columns: [0,1] // Column index which needs to export
// 								  }
// 							   },
// 							   {
// 								  extend: 'csv',
// 							   },
							   {
								  extend: 'excel',
								   title: 'Participants data'
							   } 
							 ],
                    pageLength: 50,
                    filter: true,
                    deferRender: true,
                    fixedHeader: {
                        header: true,
                        footer: true
                      },
                      rowCallback: function(row, data) {
                          jQuery(row).attr('title', data[0])
                        },
				   
                });
  </script>
<?php
}
