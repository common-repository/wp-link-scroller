<?php
    add_action( 'init', 'wpls_option_fields', 10 );
    function wpls_option_fields(){
        global $wpls;
		if ( $wpls )
		$wpls->fields = array ( 
			'main_section'	=>	array( array( 'Main Settings', '' ), array(
				'offset'	=> array( 'number', 'Offset', array( 'default' => 80 ) ),
				'delay'		=> array( 'number', 'Delay', array( 'default' => 1500, 'step' => 50 ) ),
				'noscroll'	=> array( 'text', 'Non-scrolling CSS-classes (delimiter ",")', array( 'default' => 'wpls_noscroll' ) ),
				)),
		);
    }