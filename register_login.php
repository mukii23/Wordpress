/***** User registration functionality *****/

function registration_form() {
	
 ob_start(); ?>	
		<h3 class="cstheading"><?php _e('Register New Account'); ?></h3>
    <style>
    div {
        margin-bottom:2px;
    }
    
    input[type=text], input[type=password]{
        margin-bottom:10px;
		width: 100%;
		height: 35px;
    }
	.passcontains:before {
		position: relative;
		left: -15px;
		content: "✖";
	}
	.passed:before {
		position: relative;
		left: -15px;
		content: "✔";
	}
	.flex-box{
		display: flex;
	}
	form#signup label{
	    line-height: 1;
		width: 100%;
		display: inline-flex;
	}
	span.passcontains {
		width: calc(50% - 22px);
		display: inline-block;
	}
	.requirements {
		padding: 20px 25px;
		font-size: 13px;
    	background-color: gainsboro;
		margin-bottom: 20px;
	}
		.register_button{
			background-color: #f6931e;
			color: #fff;
			padding: 10px 15px;
			border: none;
			border-radius: 3px;
			margin-top: 30px;
		}
    </style>
   
 

    <form id="signup" action="" method="post">
		<div class="flex-box">
			<div class="et_pb_column et_pb_column_1_2 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
				<label for="firstname">First Name</label>
				<input type="text" name="fname" value="">
			</div>

			<div class="et_pb_column et_pb_column_1_2 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
				<label for="website">Last Name</label>
				<input type="text" name="lname" value="">
			</div>
		</div>
		<div class="flex-box" style="margin-bottom: 15px;">
			<div class="et_pb_column et_pb_column_1_2 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
				<label for="email">Email <strong>*</strong></label>
				<input type="text" id="email" name="email" value="">
				<h5 id="emailcheck" style="color: red;">
					**Please enter a valid email address
				</h5>
			</div>
		</div>
	
	
	<div class="panel panel-default" style="transform: translateY(0px); opacity: 1; height: auto;">
		<div class="panel-heading">
			<h5 class="text-center">Password Requirements:</h5>
		</div>
		<div class="requirements">
		    <span id="length" class="passcontains">8 Characters</span>
			<span id="letter" class="passcontains">1 Lowercase</span>
			<span id="capital" class="passcontains">1 Uppercase</span>
			<span id="number" class="passcontains">1 Number</span>
		</div>
	</div>
    <div class="flex-box">
		<div id="usernameField" class="et_pb_column et_pb_column_1_2 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
			<label for="password">Password <strong>*</strong></label>
			<input type="password" id="password" name="password" value="">
			<div class="flex-box">
				<input type="checkbox" id="ShowPassword" name="ShowPassword" value="">
				<label for="ShowPassword">Show Password</label>
			</div>
			<h5 id="passcheck" style="color: red;">
				**Please Fill the password
			</h5>
		</div>

		<div class="et_pb_column et_pb_column_1_2 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
			<label for="password"> Confirm Password <strong>*</strong></label>
			<input type="password" id="chkpassword" name="chkpassword" value="">
			<div class="flex-box">
				<input type="checkbox" id="ShowChkPassword" name="ShowChkPassword" value="">
				<label for="ShowChkPassword">Show Password</label>
			</div>
			<h5 id="conpasscheck" style="color: red;">
				  **Your passwords do not match.
			</h5>
		</div>
	</div>
     
    <input type="submit" id="submitRegBtn" name="submit" value="Create Account" class="register_button" />
    </form>
    <?php
	return ob_get_clean();
}

function registration_validation($first_name, $last_name, $email, $password, $chkpassword )  {
     global $reg_errors;
     $reg_errors = new WP_Error;

	if ( empty( $password ) || empty( $email ) ) {
		$reg_errors->add('field', 'Required form field is missing');
	}
	if ( 5 > strlen( $password ) ) {
        $reg_errors->add( 'password', 'Password length must be greater than 5' );
    }
	if ( !is_email( $email ) ) {
		$reg_errors->add( 'email_invalid', 'Email is not valid' );
	}
	if ( email_exists( $email ) ) {
		$reg_errors->add( 'email', 'Email Already in use' );
	}
	
	if ( is_wp_error( $reg_errors ) ) {

		foreach ( $reg_errors->get_error_messages() as $error ) {

			echo '<div>';
			echo '<strong>ERROR</strong>:';
			echo $error . '<br/>';
			echo '</div>';

		}
	}  
}

function complete_registration() {
    global $reg_errors, $first_name, $last_name, $email, $password, $chkpassword;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $email,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
		'show_admin_bar_front' => 'false' 
        );
        $new_user_id = wp_insert_user( $userdata );
		if($new_user_id) {
				// send an email to the admin alerting them of the registration
				//wp_new_user_notification($new_user_id);
 
				// log the new user in
				wp_setcookie($email, $password, true);
				wp_set_current_user($new_user_id, $email);	
				do_action('wp_login', $email);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url()); exit;
			}
    }
}


function custom_registration_function() {
	global $first_name, $last_name, $email, $password, $chkpassword;
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
			$_POST['fname'],
			$_POST['lname'],
			$_POST['email'],
			$_POST['password'],
			$_POST['chkpassword']
        );
         
        // sanitize user form input
        $password   =   esc_attr( $_POST['password'] );
        $chkpassword   =   esc_attr( $_POST['chkpassword'] );
        $email      =   sanitize_email( $_POST['email'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
			$first_name,
			$last_name,
			$email,
			$password,
			$chkpassword
        );

	} 
}
add_action('init', 'custom_registration_function');

// Register a new shortcode: [lendco_custom_registration]
add_shortcode( 'lendco_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
	if(!is_user_logged_in()) {
	    $output = registration_form();
	} else {
		$output = __('User registration is not enabled for already loggein user');
	}
	return $output;
}


/***** End User registration functionality *****/



function lendco_login_form_shortcode( $atts, $content = null ) {
 
	extract( shortcode_atts( array(
      'redirect' => ''
      ), $atts ) );
    $form='';
	if (!is_user_logged_in()) {
		if($redirect) {
			$redirect_url = $redirect;
		} else {
			$redirect_url = "http://totalenergyroofandsolar.com/refer-a-friend/";
		}
		$form = '<form name="loginform" id="loginform" method="post">
		            <span class="invalid-feedback invalid_user"></span>
		            '.wp_nonce_field('lendco-login-nonce', 'csrf_security').'
					<p class="login-username">
					    <label for="user_login">Email</label>
					    <div class="wp-email">
							<input type="text" name="user_name" id="user_login" class="input" value="" autocomplete="off">
							<span class="invalid-feedback">Email is missing.</span>
						</div>
					</p>
					<p class="login-password">
						<label for="user_pass">Password</label>
						<div class="wp-pwd">
							<input type="password" name="user_password" id="user_pass" class="input" value="" autocomplete="off">         
							<span class="invalid-feedback">Password is missing.</span>
						</div>
					</p> 

					<p class="login-submit">
						<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Sign In" autocomplete="off">
						<input type="hidden" name="redirect_to" value="'.$redirect_url.'" autocomplete="off">
					</p>
                    <p>If you do not have account with us, please <a href ="/register">click here</a> to <a href ="/register">register</a> here first then you an refer a customers who need our service</if>
				</form>';
	}else{
		$form = 'You are already in. Please explore Total Enegry Roof and Solar website. You can refer your friend to us <a href="/refer-a-friend">here</a>';
	} 
	return $form;
}
add_shortcode('lendco_custom_login', 'lendco_login_form_shortcode');




add_action( 'wp_ajax_lendcologin', 'lendcologin_login_auth' );
add_action( 'wp_ajax_nopriv_lendcologin', 'lendcologin_login_auth' );
function lendcologin_login_auth(){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	// Here you verify your nonce
    if ( ! wp_verify_nonce( $_POST['csrf_security'], 'lendco-login-nonce' ) ) {
        // You can either return, or use nifty json stuffs
        wp_send_json_error();
    }

    $usercreds = array();
    $usercreds['user_login'] = $_POST['user_name'];
    $usercreds['user_password'] = $_POST['user_password'];
    $usercreds['remember'] = false ;
    
    $user = wp_signon( $usercreds, false );
 
    if ( is_wp_error( $user ) ) {
		echo json_encode(array('loggedin'=>false, 'message'=>__($user->get_error_message())));
    }else{
		echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...'),'redirect'=>$_POST['redirect_to']));
	}
	die();
}

add_filter( 'login_errors', function( $error ) {
    global $errors;
    $err_codes = $errors->get_error_codes();
 
    // Invalid username.
    // Default: '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password</a>?'
    if ( in_array( 'invalid_email', $err_codes ) ) {
        $error = 'Unknown email address. Try the same one you used for signup.';
    }
 
    return $error;
} );

//************* jQuery Code ****************//
jQuery('#emailcheck').hide();
		let emailError = true;
		jQuery('#email').keyup(function () {
			validateEmail();
		});
		function validateEmail() {
			let emailValue =
				jQuery('#email').val();
			let regex =/^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
			if(regex.test(emailValue)){
				jQuery('#emailcheck').hide();
				emailError = true;
			}
			else{
			   jQuery('#emailcheck').show();
			   emailError = false;
			}
		}

		jQuery('#conpasscheck').hide();
		let confirmPasswordError = true;
		jQuery('#chkpassword').keyup(function () {
			validateConfirmPassword();
		});
		function validateConfirmPassword() {
			let confirmPasswordValue = jQuery('#chkpassword').val();
			let passwordValue = jQuery('#password').val();
			if (confirmPasswordValue.length == '') {
				jQuery('#conpasscheck').html("**Please Fill the password");
				jQuery('#conpasscheck').show();
				confirmPasswordError = false;
			}else {
				jQuery('#conpasscheck').hide();
			}

			if(confirmPasswordValue.length>0){
				if (passwordValue != confirmPasswordValue) {
					jQuery('#conpasscheck').show();
					jQuery('#conpasscheck').html("**Your passwords do not match.");
					jQuery('#conpasscheck').css("color", "red");
					confirmPasswordError = false;
				} else {
					jQuery('#conpasscheck').html("**Please Fill the password");
					jQuery('#conpasscheck').hide();
					confirmPasswordError = true;
				}
			}
		}

		 // Validate Password
		jQuery('#passcheck').hide();
		let passwordError = true;
		jQuery('#password').keyup(function () {
			validatePassword();
		});
		function validatePassword() {
			let passwordValue =  jQuery('#password').val();
			if (passwordValue.length == '') {
				jQuery('#passcheck').html("**Please Fill the password");
				jQuery('#passcheck').show();
				passwordError = false;
			}else {
				jQuery('#passcheck').hide();
			}


			// Validate lowercase letters
			  var lowerCaseLetters = /[a-z]/g;
			  if(passwordValue.match(lowerCaseLetters)) {  
				jQuery('#letter').addClass('passed');
			  } else {
				jQuery('#letter').removeClass('passed');
			  }

			  // Validate capital letters
			  var upperCaseLetters = /[A-Z]/g;
			  if(passwordValue.match(upperCaseLetters)) {  
				jQuery('#capital').addClass('passed');
			  } else {
				jQuery('#capital').removeClass('passed');
			  }

			  // Validate numbers
			  var numbers = /[0-9]/g;
			  if(passwordValue.match(numbers)) {  
				jQuery('#number').addClass('passed');
			  } else {
				jQuery('#number').removeClass('passed');
			  }

			  // Validate length
			  if(passwordValue.length >= 8) {
				jQuery('#length').addClass('passed');
			  } else {
				jQuery('#length').removeClass('passed');
			  }
			  if(passwordValue.length > 0){
				  if(jQuery('.passed').length==4){
					   jQuery('#passcheck').hide();
				  }else{
					  jQuery('#passcheck').html("**This password does not meet our requirements");
					  jQuery('#passcheck').show();
				  }
			  }
		}




		jQuery('#password').blur(function(){
			if( !jQuery(this).val() ) {
				  jQuery('#passcheck').html("**Please Fill the password");
				  jQuery('#passcheck').show();
			}
		});
		jQuery('#chkpassword').blur(function(){
			if( !jQuery(this).val() ) {
				  jQuery('#conpasscheck').html("**Please Fill the password");
				  jQuery('#conpasscheck').show();
			}
		});
		jQuery('#ShowPassword').click(function () {  
			jQuery('#password').attr('type', jQuery(this).is(':checked') ? 'text' : 'password');  
		});  
		jQuery('#ShowChkPassword').click(function () {  
			jQuery('#chkpassword').attr('type', jQuery(this).is(':checked') ? 'text' : 'password');  
		});  

		jQuery('#submitRegBtn').click(function () {
			validatePassword();
			validateConfirmPassword();
			validateEmail();
			if ((passwordError == true) &&
				(confirmPasswordError == true) &&
				(emailError == true)) {
				return true;
			} else {
				return false;
			}
		});

		var baseUrl = ajax_auth_object.ajaxurl;

		var error = 0;
		jQuery('.invalid_user').hide();
		jQuery('#user_login').next().hide();
		jQuery('#user_pass').next().hide();
		jQuery('#user_login').keyup(function () {
			validateLoginEmail();
		});
		function validateLoginEmail() {
			let emailValue =
				jQuery('#user_login').val();
			let regex =/^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
			if(regex.test(emailValue)){
				jQuery('#user_login').next().hide();
				error = 0;
			}
			else{
			   jQuery('#user_login').next().html('Enter a valid email');
			   jQuery('#user_login').next().show();
			   error = 1;
			}
		}
		//login form submission
		jQuery('#loginform').find('#wp-submit').on('click', function (e) {
			e.preventDefault();

			var userlogin = jQuery('#user_login').val();
			var userpass = jQuery('#user_pass').val();

			if(userlogin.length < 1){
				jQuery('#user_login').next().show();
				error = 1;
			}

			if(userpass.length < 1){
				jQuery('#user_pass').next().show();
				error = 1;
			}

			if(error==1){
				return false;
			}

				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: baseUrl,
					data: jQuery('#loginform').serialize()+ "&action=lendcologin",
					beforeSend: function(){
						jQuery("#wp-submit").val('Processing');
					},

					success: function (data) {
						if(data.loggedin == false){

							if(!data.error_type){
								jQuery('.invalid_user').html(data.message);
								jQuery('.invalid_user').show();
							}						
							jQuery('#loginform').trigger('reset');
							jQuery("#wp-submit").val('Sign In');
						}else if(data.loggedin == true) {
							document.location.href = data.redirect;
// 							window.location.replace(data.redirect);
// 							setTimeout(location.reload(true),1500);
						}
					},
					error: function(xhr, status, error){
						 var errorMessage = xhr.status + ': ' + xhr.statusText
						 alert('Error - ' + errorMessage);
					}     
				});
		});
