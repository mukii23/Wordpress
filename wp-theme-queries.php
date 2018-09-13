    /***********************************************************
     ****Add Footer Section Settings in  Theme Customizer*******
     ***********************************************************/
    
    function add_custom_settings($wp_customize){
        
        $wp_customize->add_section( 'footer_section' , array(
            'title'      => 'Footer Section',
            'priority'   => 60,
        ) );
        $wp_customize->add_setting( 'footer_text' , array(
            
//            'default' => 'Copyright © 2018  NewsClues. All Rights Reserved.',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_text', array(
            'type' => 'textarea',
            'label'        => 'Input text for footer here.',
            'section'    => 'footer_section',
            'settings'   => 'footer_text',
        ) ) );
        
        
    }
    
    add_action( 'customize_register', array($this, 'add_custom_settings' ) );
