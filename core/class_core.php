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
		add_action( 'init', array( &$this, 'init_upper' ), 0 );
	}
	/**
	 * Initialize plugin
	 * 
	 */
	function init_upper() {
		/** Initialize the widget */
		add_action( 'widgets_init', create_function( '', 'register_widget("MMM_Weather");' ) );
		
		/** Load widget js file */
		wp_register_script( 'mmm-weather-widget-jscript', MMMW_URL . '/js/scripts.js', array( 'jquery' ), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-widget-jscript');
						
		/** Load widget css file */
		wp_register_style( 'mmm-weather-widget-style', MMMW_URL . '/css/style.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-widget-style');

		/** Initialize the shortcode */
		add_shortcode( 'mmm-weather', array( &$this, 'shortcode_mmm_weather' ) );
		
		/** Add the AJAX actions for both logged in and not logged in */
		add_action( 'wp_ajax_nopriv_mmmweather_submit', array( &$this, 'mmmweather_submit' ) );
		add_action( 'wp_ajax_mmmweather_submit', array( &$this, 'mmmweather_submit' ) );

		/** Admin page */
		add_action( 'admin_menu', array( &$this, 'mmm_weather_page_menu' ) );
						
		/** Run the Updater if admin */
		add_action( 'admin_init', create_function( '', 'new WP_Github_Updater;' ) );
				
		/** Hook for workaround for WordPress getting SSL certificate at GitHub */
		add_action('http_request_args', array( &$this, 'jkudish_http_request_args'), 10, 2 );
	}
	/**
	 * MMM Weather Page submenu
	 */
	function mmm_weather_page_menu() {
		add_submenu_page( 'edit.php?post_type=page', 'MMM Weather', 'MMM Weather', 'manage_options', 'mmm-weather-admin', 'MMMW_Core::mmm_weather_admin' );
	}
	/**
	 * MMM Weather Page Admin content
	 */
	function mmm_weather_admin() {
		// check that the user has the required capability
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __('You do not have sufficient permissions to access this page.' ) );
		}
		
		echo '<div class="wrap">';
		echo "<h2>" . __( 'MMM Weather', 'mmm-weather-admin' ) . "</h2>";
		echo "<p>" . __( 'MMM Weather uses an XML API Feed from Environment Canada. For more information: <a href="http://dd.weatheroffice.gc.ca/about_dd_apropos.txt">About MSC HTTP Data Server</a>', 'mmm-weather-admin' ) . "</p>";
		echo "<p>" . __( 'Use') . "<code>[mmm-weather]</code>" . __( 'shortcode anywhere in your page.' ) . "</p>";
		echo "<p>" . __( 'Widget is currently hard coded to Revelstoke Weather. Future upgrade will have ability to set any Environment Canada city.' ) . "</p" ;
	}
	/**
	 * MMM Weather Page from shorcode [mmm-weather]
	 * @param the shortcode name $atts
	 * @param shortcode params $content
	 */
	function shortcode_mmm_weather( $atts, $content=null ) {
		/** Load page js file */
		wp_register_script( 'mmm-weather-page-jscript', MMMW_URL . '/js/weather-page.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-page-jscript');
		wp_localize_script( 'mmm-weather-page-jscript', 'MMMWeather', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		/** Load page css file */
		wp_register_style( 'mmm-weather-page-style', MMMW_URL . '/css/weather-page.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-page-style');
		
		/** HTML Output */
		$html = '<div id="change-location">
				<form>
					<select id="wx-location">
						<option value="">Change Mountain Towns</option>
						<option value="revelstoke">Revelstoke</option>
						<option value="nelson">Nelson</option>
						<option value="golden">Golden</option>
						<option value="banff">Banff</option>
						<option value="canmore">Canmore</option>
						<option value="calgary">Calgary</option>
						<option value="whistler">Whistler</option>
						<option value="squamish">Squamish</option>
						<option value="vancouver">Vancouver</option>
					</select> 
				</form>	
			</div>
			<div id="mmm-weather-page-container"></div>
			<div id="mmm-weather-page-loader"></div>';
		return $html;
	}
	/**
	 * jQuery AJAX Function
	 * returns new location weather html
	 */	
	function mmmweather_submit() {
				
		// switch depending on selection
		switch ($_POST['location']) {
			case 'golden' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000527_e.xml' );
				break;
		
			case 'revelstoke' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000679_e.xml' );
				break;
		
			case 'nelson' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000258_e.xml' );
				break;
		
			case 'banff' :
				$resp['display'] = MMMW_Core::display_weather( 'AB/s0000404_e.xml' );
				break;
		
			case 'canmore' :
				$resp['display'] = MMMW_Core::display_weather( 'AB/s0000403_e.xml' );
				break;
		
			case 'calgary' :
				$resp['display'] = MMMW_Core::display_weather( 'AB/s0000126_e.xml' );
				break;
		
			case 'whistler' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000078_e.xml' );
				break;
		
			case 'squamish' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000323_e.xml' );
				break;
		
			case 'vancouver' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000141_e.xml' );
				break;
		}

		// send display response back to jQuery
		header( "Content-Type: application/json" );
		echo json_encode( $resp );
		exit;
	}
	/**
	 * Output the Weather Forecast
	 * @param location url $xml
	 */
	function display_weather( $xml ) {
		$ajax_weather_url = 'http://dd.weatheroffice.gc.ca/citypage_weather/xml/' . $xml;
		$AgetHeaders = @get_headers( $ajax_weather_url );

		$wxhtml = '';
		
		// check if file exists
		if ( preg_match( "|200|", $AgetHeaders[0] ) ) {
			$ajax_weather = simplexml_load_file( $ajax_weather_url );
		
			$wxhtml .= '<div id="location">' . $ajax_weather->currentConditions->station . '</div>';
			$wxhtml .= '<div id="date">' . $ajax_weather->dateTime[1]->textSummary . '</div>';
			$wxhtml .= '<div id="current-conditions">';
			// check to see if icon code is available
			if ( !empty( $ajax_weather->currentConditions->iconCode ) ) {
				$wxhtml .= '<div id="icon"><img src="' . MMMW_ICON_URL . $ajax_weather->currentConditions->iconCode . '.png" width="120" /></div>';
			} else {
				$wxhtml .= '<div id="icon"><img src="' . MMMW_ICON_URL . '29.png" width="120" /></div>';
			}
			$wxhtml .= '<div id="temp-sky-container">';
			$wxhtml .= '<div id="temperature">' . $ajax_weather->currentConditions->temperature . '&deg;C</div>';
			$wxhtml .= '<div id="sky">' . $ajax_weather->currentConditions->condition . '</div>';
			$wxhtml .= '</div>';
			$wxhtml .= '</div>';
			$wxhtml .= '<div id="current-data">';
			$wxhtml .= 'Wind <strong>' . $ajax_weather->currentConditions->wind->speed . 'km/h' . $ajax_weather->currentConditions->wind->direction . '</strong><br />';
			$wxhtml .= 'Dewpoint <strong>' . $ajax_weather->currentConditions->dewpoint . '&deg;C</strong><br />';
			$wxhtml .= 'Windchill <strong>' . $ajax_weather->currentConditions->windChill . '&deg;C</strong><br />';
			$wxhtml .= 'Barometer <strong>' . $ajax_weather->currentConditions->pressure . 'kPa</strong><br />';
			$wxhtml .= 'Relative Humidity <strong>' . $ajax_weather->currentConditions->relativeHumidity . '%</strong><br />';
			$wxhtml .= 'Visibility <strong>' . $ajax_weather->currentConditions->visibility . '</strong>km';
			$wxhtml .= '</div>';
			$wxhtml .= '<div class="clear"></div>';
			$wxhtml .= '<div id="forecast">Extended Forecast</div>';
		
			// loop through forecasts
			$forecastNode = $ajax_weather->xpath( 'forecastGroup/forecast' );
			$forecastCount = count( $forecastNode ); //max number of forecast periods
		
			for ( $i = 0; $i < $forecastCount; $i++ ) {
				$wxhtml .= '<div id="forecast-period">';
				$wxhtml .= '<div id="icon">';
				$wxhtml .= '<img src="' . MMMW_ICON_URL . $ajax_weather->forecastGroup->forecast[$i]->abbreviatedForecast->iconCode .'.png" />';
				$wxhtml .= '</div>';
				$wxhtml .= '<div id="period">' . $ajax_weather->forecastGroup->forecast[$i]->period . '</div>';
				$wxhtml .= '<div id="text-summary">' . $ajax_weather->forecastGroup->forecast[$i]->textSummary . '</div>';
				$wxhtml .= '</div>';
			} // end forecast loop 
		} // end if file exists 
						
		return $wxhtml;
	}
	/**
	 *  Current workaround for WordPress getting SSL certificate at GitHub
	 *  @param array $args
	 *  @param url $url
	 */
	function jkudish_http_request_args( $args, $url ) {
		$args['sslverify'] = false;
		return $args;
	}
}
?>