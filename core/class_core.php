<?php
/**
 * MMM Weather Core Class
 * 
 * @version 2.0.0
 * @author Derek Marcinyshyn
 * @package MMM Weather
 * @subpackage Core
 * 
 */
class MMMW_Core {
	
	/**
	 * Highest-level function initialized on plugin load
	 * 
	 */
	
	function MMMW_Core() {
		
		/** Hook in upper init */
		add_action( 'init', array($this, 'init_upper' ), 0);
	}
	
	/**
	 * Initialize plugin
	 * 
	 */
	function init_upper() {

		/** Initialize the widget */
		add_action( 'widgets_init', create_function( '', 'register_widget("MMM_Weather");' ) );
		
		/** Load js file */
		wp_register_script( 'mmm-weather-jscript', MMMW_URL . '/js/scripts.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-jscript');
						
		/** Load css file */
		wp_register_style( 'mmm-weather-style', MMMW_URL . '/css/style.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-style');
		//add_action( 'wp_print_styles', array($this, 'add_mmm_weather_style' ) );
				
		/** Run the Update if admin */
		if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
			$config = array(
					'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
					'proper_folder_name' => 'mmm-weather', // this is the name of the folder your plugin lives in
					'api_url' => 'https://api.github.com/repos/DerekMarcinyshyn/MMM-Weather', // the github API url of your github repo
					'raw_url' => 'https://raw.github.com/DerekMarcinyshyn/MMM-Weather/master/', // the github raw url of your github repo
					'github_url' => 'https://github.com/DerekMarcinyshyn/MMM-Weather', // the github url of your github repo
					'zip_url' => 'https://github.com/DerekMarcinyshyn/MMM-Weather/zipball/master', // the zip url of the github repo
					'requires' => '3.0', // which version of WordPress does your plugin require?
					'tested' => '3.3', // which version of WordPress is your plugin tested up to?
			);
			new wp_github_updater( $config );
		} 
		
		/** Hook for workaround for WordPress getting SSL certificate at GitHub */
		add_action('http_request_args', array($this, 'jkudish_http_request_args'), 10, 2 );
	}
		
	/**
	 *  Current workaround for WordPress getting SSL certificate at GitHub
	 * 
	 */
	function jkudish_http_request_args($args, $url) {
		$args['sslverify'] = false;
		return $args;
	}
}
?>