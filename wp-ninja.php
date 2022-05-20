/*****************************
==== After Ninja form submisison ===
*****************************/
$submissions = Ninja_Forms()->form( 2 )->get_subs();
if ( is_array( $submissions ) && count( $submissions ) > 0 ) {
    foreach($submissions as $submission) {
        // print_r($submission);
        if($submission->get_seq_num() == $Membership_ID){
            $u_values = $submission->get_field_values();
            $name = $u_values['user_name'];
            $address = $u_values['permanent_address'];
            $bgroup = $u_values['blood_group'];
            $subdate = $submission->get_sub_date();
            $photo = $u_values['passport_size_photo'];
            echo $name.'<br/>';
            echo $address.'<br/>';
            echo $bgroup.'<br/>';
            echo $subdate.'<br/>';
            $processedphoto = unserialize($photo);
            foreach($processedphoto as $processedphotos){
                echo '<img src="'.$processedphotos.'" />';
            }
        }
        
    }
}
