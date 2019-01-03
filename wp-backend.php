 /** 
 **** Add the user's mobile number to their profile screen.
 */
 

add_action('show_user_profile', 'profile_fields');
add_action('edit_user_profile', 'profile_fields');

function profile_fields( $user ) { ?>
   <h3><?php _e("Extra Profile Information", "blank"); ?></h3>

   <table class="form-table">
       <tr>
           <th><label for="mobile"><?php _e("Mobile"); ?></label></th>
           <td>
               <input type="text" name="mobile" id="mobile" value="<?php echo esc_attr( get_the_author_meta( 'new_mobile', $user->ID ) ); ?>" class="regular-text" /><br />
               <span class="description"><?php _e("Please enter your mobile number, including the country code, for SMS notifications. By entering your mobile number you are consenting to be notified by SMS of notifications and standard messaging rates apply."); ?></span>
           </td>
       </tr>
   </table>
<?php }

add_action( 'personal_options_update', 'save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_user_profile_fields' );

function save_user_profile_fields( $user_id ) {

   if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

   update_user_meta( $user_id, 'new_mobile', $_POST['mobile'] );
}
