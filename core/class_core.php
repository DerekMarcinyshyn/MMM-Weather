<?php
/**
 * MMM Weather Core Class
 * 
 * @version 2.0.2
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
		/* Hook in upper init */
		add_action( 'init', array( &$this, 'init_upper' ), 0 );
	}
	/**
	 * Initialize plugin
	 * 
	 */
	function init_upper() {
		/* Initialize the widget */
		add_action( 'widgets_init', create_function( '', 'register_widget("MMM_Weather");' ) );
		
		/* Load widget js file */
		wp_register_script( 'mmm-weather-widget-jscript', MMMW_URL . '/js/scripts.js', array( 'jquery' ), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-widget-jscript');
						
		/* Load widget css file */
		wp_register_style( 'mmm-weather-widget-style', MMMW_URL . '/css/style.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-widget-style');

		/* Initialize the shortcode */
		add_shortcode( 'mmm-weather', array( &$this, 'shortcode_mmm_weather' ) );
		
		/* Add the AJAX actions for both logged in and not logged in */
		add_action( 'wp_ajax_nopriv_mmmweather_submit', array( &$this, 'mmmweather_submit' ) );
		add_action( 'wp_ajax_mmmweather_submit', array( &$this, 'mmmweather_submit' ) );

		/* Admin page */
		add_action( 'admin_menu', array( &$this, 'mmm_weather_page_menu' ) );
						
		/* Run the Updater if admin */
		add_action( 'admin_init', create_function( '', 'new WP_Github_Updater;' ) );
		
		/* SSL Verify workaround */
		add_action( 'http_request_args', array( &$this, 'mmm_ssl_workaround' ), 10, 2 );
	}
	/**
	 * MMM SSL Verify workaround for GitHub / WordPress
	 * @param $args array sslverify => false
	 * @param $url
	 * @return $args
	 */
	function mmm_ssl_workaround( $args, $url ) {
		$args['sslverify'] = false;
		return $args;
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
		/* check that the user has the required capability */
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __('You do not have sufficient permissions to access this page.' ) );
		}
		
		echo '<div class="wrap">';
		echo "<h2>" . __( 'MMM Weather', 'mmm-weather-admin' ) . "</h2>";
		echo "<p>" . __( 'MMM Weather uses an XML API Feed from Environment Canada. For more information: <a href="http://dd.weatheroffice.gc.ca/about_dd_apropos.txt">About MSC HTTP Data Server</a>', 'mmm-weather-admin' ) . "</p>";
		echo "<p>" . __( 'Use') . "<code>[mmm-weather]</code>" . __( 'shortcode anywhere in your page.' ) . "</p>";
		echo "<p>" . __( 'Widget is currently hard coded to Revelstoke Weather. Future upgrade may have ability to set any Environment Canada city.' ) . "</p" ;
	}
	/**
	 * MMM Weather Page from shorcode [mmm-weather]
	 * @param the shortcode name $atts
	 * @param shortcode params $content
	 */
	function shortcode_mmm_weather( $atts, $content=null ) {
		/* Load page js files */
		wp_register_script( 'mmm-weather-page-jscript', MMMW_URL . '/js/weather-page.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-page-jscript');
		
		wp_localize_script( 'mmm-weather-page-jscript', 'MMMWeather', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_register_script( 'mmm-weather-page-jquery-ui', MMMW_URL . '/js/jquery-ui-1.8.17.custom.min.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-page-jquery-ui');
		
		wp_register_script( 'mmm-weather-page-fancybox', MMMW_URL . '/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-page-fancybox');
		
		wp_register_script( 'mmm-weather-page-fancybox-mousewheel', MMMW_URL . '/js/fancybox/jquery.mousewheel-3.0.4.pack.js', array('jquery'), MMMW_VERSION, true );
		wp_enqueue_script( 'mmm-weather-page-fancybox-mousewheel');
		
		/* Load page css files */
		wp_register_style( 'mmm-weather-page-style', MMMW_URL . '/css/weather-page.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-page-style');
		
		wp_register_style( 'mmm-weather-jquery-ui-style', MMMW_URL . '/css/smoothness/jquery-ui-1.8.17.custom.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-jquery-ui-style');
		
		wp_register_style( 'mmm-weather-fancybox-style', MMMW_URL . '/js/fancybox/jquery.fancybox-1.3.4.css', false, MMMW_VERSION );
		wp_enqueue_style( 'mmm-weather-fancybox-style');
		
		/* HTML Output */
		$html ='<div id="weather-tabs">
					<ul>
						<li><a href="#city-forecasts">Forecasts</a></li>
						<li><a href="#infrared">Infrared</a></li>
						<li><a href="#jet">Jet Stream</a></li>
						<li><a href="#fronts">48 Hour Fronts</a></li>
						<li><a href="#seven">7 Day Forecast</a></li>
					</ul>
					
					<div id="city-forecasts">
						<div id="change-location">
							<form>
								<select id="wx-location">
									<option value="">Change Forecast City</option>
									<optgroup label="Columbia Mountains">
										<option value="revelstoke">Revelstoke</option>
										<option value="golden">Golden</option>
										<option value="nelson">Nelson</option>
									</optgroup>
									<optgroup label="Rocky Mountains">
										<option value="banff">Banff</option>
										<option value="canmore">Canmore</option>
										<option value="calgary">Calgary</option>
										<option value="sparwood">Sparwood/Fernie</option>
									</optgroup>
									<optgroup label="Okanagan">
										<option value="salmon-arm">Salmon Arm</option>
										<option value="vernon">Vernon</option>
										<option value="kelowna">Kelowna</option>
										<option value="penticton">Penticton</option>
										<option value="kamloops">Kamloops</option>
									</optgroup>
									<optgroup label="Coast">
										<option value="whistler">Whistler</option>
										<option value="squamish">Squamish</option>
										<option value="vancouver">Vancouver</option>
										<option value="tofino">Tofino</option>
										<option value="campbell-river">Campbell River</option>
										<option value="nanaimo">Nanaimo</option>
										<option value="victoria">Victoria</option>
									</optgroup>
								</select> 
							</form>	
						</div><!-- #change-location -->
						<div id="mmm-weather-page-container"></div>
						<div id="mmm-weather-page-loader"></div>
					</div><!-- #city-forecasts -->
						
					<div id="infrared">
						<a id="fb-infrared" href="http://squall.sfsu.edu/gif/sathts_pac_500_00.gif"><img src="http://squall.sfsu.edu/gif/sathts_pac_500_00.gif" alt="GOES-West Infrared Image" /></a>
					</div><!-- #satellite -->

					<div id="jet">
						<a id="fb-jet" href="http://virga.sfsu.edu/gif/jetstream_pac_init_00.gif"><img src="http://virga.sfsu.edu/gif/jetstream_pac_init_00.gif" alt="300mb Jet Stream" /></a>
					</div><!-- #jet -->
					
					<div id="fronts">
						<a id="fb-fronts" href="http://www.atmos.washington.edu/~ovens/loops/wxloop.cgi?fronts_ir+/48h/">Click to open 48 Hour Fronts from University of Washington<br /><img src="'.MMMW_URL.'/images/fronts.jpg" alt="Click to open animated west coast fronts" title="Click to open animated west coast fronts" /></a>
					</div><!-- #fronts -->
					
					<div id="seven">
						<a id="fb-seven" href="http://www.atmos.washington.edu/~ovens/loops/wxloop.cgi?gfs_pcpn_slp_thkn+///6">Click to open 7 Day Forecast from University of Washington<br /><img src="'.MMMW_URL.'/images/seven.jpg" alt="Click to open animated west coast fronts" title="Click to open 7 Day Forecast" /></a>
					</div><!-- #seven -->
						
				</div><!-- #weather-tabs -->	
						
				';
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
				
			case 'salmon-arm' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000324_e.xml' );
				break;
				
			case 'vernon' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000216_e.xml' );
				break;
			
			case 'kelowna' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000592_e.xml' );
				break;
				
			case 'penticton' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000772_e.xml' );
				break;
				
			case 'kamloops' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000568_e.xml' );
				break;
				
			case 'sparwood' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000175_e.xml' );
				break;		

			case 'tofino' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000481_e.xml' );
				break;
				
			case 'campbell-river' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000488_e.xml' );
				break;
				
			case 'nanaimo' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000496_e.xml' );
				break;
			
			case 'victoria' :
				$resp['display'] = MMMW_Core::display_weather( 'BC/s0000775_e.xml' );
				break;
		}

		/* send display response back to jQuery */
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
			$wxhtml .= '<div id="current-conditions-container">';
			$wxhtml .= '<div id="current-conditions">';
			
			/* check to see if icon code is available */
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
			$wxhtml .= '</div></div><!-- current-conditions-container -->';
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
}
?>