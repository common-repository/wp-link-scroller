<?php
	#	wpls Admin menu init
	add_action( 'admin_menu', 'wpls_menu_init', 50 );
	function wpls_menu_init() {
		global $wpls;
		if ( ! isset( $wpls ) ) return;
			
			do_action( 'wpls_before' ) ;
			
			add_options_page( 'WP Link Scroller', 'WP Link Scroller', 'manage_options', 'wpls_settings_page', 'wpls_menu_cbf');
			function wpls_menu_cbf(){
				global $wpls;
			?>  
				<div class="wrap">  
					<h1><?php _e( 'WP Link Scroller settings page', $wpls->text_domain ) ?></h1>
					
					<?php settings_errors(); ?>
					<form method="post" action="options.php">  
						<?php settings_fields( 'wpls_settings_page' ); ?>  
						<?php do_settings_sections( 'wpls_settings_page' ); ?>             
						<?php submit_button(); ?>  
					</form>  
			  
				</div> 
			<?php  		
			}
	}
	
	add_action( 'admin_init', 'wpls_options_menu_init', 50 );
	function wpls_options_menu_init() {
			global $wpls;
			#	Registration settings
			if ( isset( $wpls->fields ) ) :
				register_setting( 'wpls_settings_page', $wpls->options_name ) ;
				foreach ( $wpls->fields as $cat => $array ) :					
					
					add_filter( 'wpls_settings_header__' . $cat, function( $h, $cat ){ global $wpls; return $wpls->fields[$cat][0][0]; }, 5, 2 );
					add_filter( 'wpls_setting_section_before', function( $descr, $cat ){ global $wpls; $cat = str_ireplace( 'wpls_setting_section__', '', $cat ); return $wpls->fields[$cat][0][1]; }, 5, 2 );
					
					#	Adding to Settings - wpls_settings_page
					add_settings_section(
						'wpls_setting_section__' . $cat,
						apply_filters( 'wpls_settings_header__' . $cat, __( 'WP Link Scroller settings', $wpls->text_domain ), $cat ),
						'wpls_setting_section_before',
						'wpls_settings_page'
					);
					foreach ( $array[ 1 ] as $key => $value ) :
						$fullname = $cat . '-' . $key ;
						$name = $wpls->options_name . '[' . $cat . '][' . $key . ']' ;
						$args = array (
							'cat'		=> $cat,
							'fullname'	=> $fullname,
							'name'		=> $name,
							'slug'		=> $key,
						) ;
						
						add_settings_field( $fullname, $value[ 1 ], 'wpls_echo_field', 'wpls_settings_page', 'wpls_setting_section__' . $cat, $args ) ;
					endforeach;
				endforeach;
			endif;
			
			do_action( 'wpls_after' ) ;
		}
	

	function wpls_setting_section_before( $args ) {
		echo apply_filters( 'wpls_setting_section_before', '', $args['id'] );
	}
	
	function wpls_echo_attrs( $attrs, $stop_list = array() ){
		
		$default = array(
			'class', 'id', 'value', 'name', 'type'
		);
		$stop_list = $stop_list + $default;
		
		if ( isset( $attrs ) ) :
			foreach( $attrs as $attr => $value ) :
				if ( in_array( $attr, $stop_list ) ) continue;
				echo esc_attr( $attr ) . '="' . esc_attr( $value ) . '" ';
			endforeach;
		endif;
	}
	
	function wpls_echo_field( $data ) {
		global $wpls;

		$value = $wpls->options[ $data[ 'cat' ] ][ $data[ 'slug' ] ] ;
		if ( $value == '' && isset( $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][2]['default'] ) )
			$value = $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][2]['default'];
		$type = $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][ 0 ];
		$clear_fullname = str_replace( array( '_', '-', ' ' ), '', $data[ 'fullname' ] );
		
		$classes = array();
		$classes[] = $data[ 'fullname' ];
		$classes[] = 'wpls-input';
		
		$html_attrs = array();
		
		#	Additional attributes
		if ( isset( $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][2] ) && is_array( $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][2] ) ) :
			$attrs = $wpls->fields[ $data[ 'cat' ] ][1][ $data[ 'slug' ] ][2];
			if ( isset( $attrs['class'] ) )
				$classes[] = $attrs['class'];
			
			if ( isset( $attrs['attrs'] ) )
				$html_attrs = $attrs['attrs'];
			
		endif;
		
		

		switch ( $type ) {
			case 'email' : ;
			case 'text' :
			?>
					<input 
						name="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						type="<?php echo esc_attr( $type ) ?>" 
						value="<?php echo esc_attr( $value ) ?>" 
						class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
						id="<?php echo esc_attr( $data[ 'fullname' ] ) ?>" 
						oninvalid="setCustomValidity( '<?php echo apply_filters( 'wpls_setCustomValidity_text', __( 'Please, check', $wpls->text_domain ), $type ); ?>' )" 
						<?php wpls_echo_attrs( $html_attrs, array( 'oninvalid' ) ); ?>
					/>	
			<?php ; break;
			case 'wysiwyg' :
			
					wp_editor( $value, esc_attr( $clear_fullname ), 
						array(
							'textarea_name'	=> esc_attr( $data[ 'name' ] ),
							'textarea_rows' => 7,
							'wpautop'		=> true
						) 
					);
				break;
			case 'checkbox' :
			?>
					<input 
						name="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						type="<?php echo esc_attr( $type ) ?>" 
						value="<?php echo '1' ?>" 
						class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
						id="<?php echo esc_attr( $data[ 'fullname' ] ) ?>" 
						<?php checked( esc_attr( $value ), '1', true ); ?> 
						<?php wpls_echo_attrs( $html_attrs, array( 'checked' ) ); ?> 
					/>	
			<?php ; break;
			case 'textarea' :
			?>
					<textarea 
						name="<?php echo esc_attr( $data['name'] ) ?>" 
						class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
						id="<?php echo esc_attr( $data['fullname'] ) ?>" 
						<?php wpls_echo_attrs( $html_attrs ); ?> 
					><?php echo esc_textarea( $value ) ?></textarea>
			<?php ; break;		
			case 'number' :
			if ( isset( $attrs ) && isset( $attrs['step'] ) )
			?>
					<input 
						name="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						type="<?php echo esc_attr( $type ) ?>" 
						value="<?php echo esc_attr( $value ) ?>" 
						class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
						id="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						oninvalid="setCustomValidity( '<?php echo apply_filters( 'wpls_setCustomValidity_text', __( 'Please, check', $wpls->text_domain ), $type ); ?>' )" 
						step=<?php echo ( isset( $attrs ) && isset( $attrs['step'] ) ) ? $attrs['step'] : 1 ; ?> 
						<?php wpls_echo_attrs( $html_attrs, array( 'oninvalid', 'step' ) ); ?>
					/>	
			<?php ; break ;			
			case 'color' : 
			?>
					<input 
						name="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						type="<?php echo esc_attr( $type ) ?>" 
						value="<?php echo esc_attr( $value ) ?>" 
						class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
						id="<?php echo esc_attr( $data[ 'name' ] ) ?>" 
						<?php wpls_echo_attrs( $html_attrs ); ?>
					/>	
			<?php ; break ;		
			case 'photo' :
				wpls_media_modal( array(
					'button_id'		=> esc_attr( $data[ 'fullname' ] ),
					'option_name'	=> esc_attr( $data[ 'name' ] ),
					'key'			=> esc_attr( $data[ 'slug' ] ),
					'data'			=> esc_attr( $value ),
					
				));
			break;
			case 'gallery' :
				wpls_media_modal( array(
					'button_id'		=> esc_attr( $data[ 'fullname' ] ),
					'multiselect'	=> true,
					'option_name'	=> esc_attr( $data[ 'name' ] ),
					'key'			=> esc_attr( $data[ 'slug' ] ),
					'data'			=> esc_attr( $value )				
				));
			break;
		default:
			echo apply_filters( 'wpls_echo_custom_field', '', $data, $type, $value );
		};
		
		$out = apply_filters( 'wpls_echo_field', ob_get_contents(), $data, $type, $value );
		ob_end_clean();
		echo $out;
	}