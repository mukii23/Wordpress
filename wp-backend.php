 /*************
 **** Add the user's mobile number to their profile screen.
 **************/
 

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

 /*************
 **** Add menu at backend with custom link
 **************/
add_action( 'admin_menu', 'dashboard_url' );
function dashboard_url() {
    add_menu_page( 'dashboard_url', 'My Menu', 'read', 'my_slug', '', 'dashicons-feedback', 1 );
}

add_action( 'admin_menu' , 'dashboard_function' );
function dashboard_function() {
    global $menu;
    $menu[1][2] = "http://www.mukii.com";
}

 /*************
 **** Add css class at backend in 'body' element
 **************/
function mk_admin_body_class( $classes ) {
    global $current_user;
    foreach( $current_user->roles as $role )
        $classes .= ' role-' . $role;
    return trim( $classes );
}
add_filter( 'admin_body_class', 'mk_admin_body_class' );

 /*************
 **** Add custom menu
 **************/
add_filter( 'wp_nav_menu_items', 'mk_menu_link', 10, 1 );
function mk_menu_link( $items ) {
   if (is_user_logged_in()) {
			   $items .= '<li class="nav-item menu-item menu-item-type-post_type menu-item-object-page"><a href="/dashboard/" class="nav-link">Dashboard</a></li>';
   }
   return $items;
}
