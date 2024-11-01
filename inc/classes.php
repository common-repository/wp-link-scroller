<?php
	namespace wpls;
	class WP_Link_Scroller{
		var
			$fields,
			$options,
			$options_name,
			$settings_name,
			$text_domain;
		
		function __construct(){
			$text_domain = wp_get_theme();
			$this->text_domain = $text_domain->get( 'TextDomain' ) ;
			$this->vars = array();
			$this->settings_name = 'wpls_settings';
			$this->options_name = 'wpls_options';
			$options = get_option( $this->settings_name );
			$this->options = get_option( $this->options_name );		
		}
		public function getOption( $option_slug ){
			$o = array();
			$option_slug = explode( '::', $option_slug );
			$o[ 'section' ] = $option_slug[ 0 ];
			$o[ 'slug' ] = $option_slug[ 1 ];
			return $this->options[ $o[ 'section' ] ][ $o[ 'slug' ] ];
		}
	}	