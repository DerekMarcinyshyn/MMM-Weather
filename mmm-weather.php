<?php
/**
* @package Wordpress
* @subpackage Widgets
* @version 0.1
*/
/*
 Plugin Name: MMM Weather
Plugin URI: http://monasheemountainmultimedia.com
Description: Revelstoke Weather Widget live feed from Environment Canada
Author: Derek Marcinyshyn
Version: 0.1
Author URI: http://monasheemountainmultimedia.com


Copyright 2011  Derek Marcinyshyn  (email : derek@monasheemountainmultimedia.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

defined('ABSPATH') or die("Cannot access pages directly.");

defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Actions and Filters
 *
 * Register any and all actions here. Nothing should actually be called
 * directly, the entire system will be based on these actions and hooks.
 */

add_action( 'widgets_init', create_function( '', 'register_widget("Revelstoke_Weather");' ) );

//load css files
function add_mmm_revelstoke_weather_style() {
	$wx_style_url = plugins_url( 'style.css', __FILE__ );
	$wx_style_file = WP_PLUGIN_DIR . '/mmm-revelstoke-weather/style.css';
	if ( file_exists( $wx_style_file ) ) {
		wp_register_style( 'mmm-revelstoke-weather-style', $wx_style_url );
		wp_enqueue_style( 'mmm-revelstoke-weather-style' );
	}
}

add_action( 'wp_print_styles', 'add_mmm_revelstoke_weather_style' );

// load js files
function add_mmm_revelstoke_weather_jscripts() {
	$wx_scripts_url = plugins_url( 'scripts.js', __FILE__ );
	$wx_scripts_file = WP_PLUGIN_DIR . '/mmm-revelstoke-weather/scripts.js';
	if ( file_exists( $wx_scripts_file ) ) {
		wp_register_script( 'mmm-revelstoke-weather-jscripts', $wx_scripts_url );
		wp_enqueue_script( 'mmm-revelstoke-weather-jscripts' );
	}
}
add_action( 'wp_enqueue_scripts', 'add_mmm_revelstoke_weather_jscripts' );



/**
 * Class
 * @constructor
 */


class Revelstoke_Weather extends WP_Widget
{
	/**
	 * Constructor
	 *
	 * Registers the widget details with the parent class
	 */
	function Revelstoke_Weather()
	{
		// widget actual processes
		parent::WP_Widget( $id = 'revelstoke_weather', $name = get_class($this), $options = array( 'description' => 'Revelstoke Weather' ) );


	}

	function form($instance)
	{
		// outputs the options form on admin
		?>

		No config.

		<?php
	}

function update($new_instance, $old_instance)
{
	// processes widget options to be saved
	$instance = wp_parse_args($old_instance, $new_instance);
	$instance['title'] = 'Revelstoke Weather';
	return $instance;
}

function widget($args, $instance)
{
	// outputs the content of the widget
	extract( $args );
	
	$wx_html =  $before_widget;
	
	// Title of Widget in Siderbar
	$wx_html .= '<h3 class="widgettitle">Revelstoke Weather</h3>';
	
	// load Environment Canada XML for Revelstoke, BC
	$weather_url = 'http://dd.weatheroffice.gc.ca/citypage_weather/xml/BC/s0000679_e.xml';
	$AgetHeaders = @get_headers( $weather_url );
	
	// check if file exists
	if ( preg_match( "|200|", $AgetHeaders[0] ) ) {
	$weather = simplexml_load_file($weather_url);
	
	//ouput weather to screen
	$wx_html .= '<div id="mmm-date">' . $weather->dateTime[1]->textSummary . '</div>';
	$wx_html .= '<div><img src="http://ds.monasheemountainmultimedia.com/wx/icons/ec/' . $weather->currentConditions->iconCode . '.png" class="mmm-weather-icon" />';
	$wx_html .= '<div id="mmm-temperature">' . $weather->currentConditions->temperature . '&deg;C</div>';
	$wx_html .= '<div id="mmm-sky">' . $weather->currentConditions->condition . '</div></div>';
	$wx_html .= '<div id="mmm-more">Click for Forecast</div>';
	$wx_html .= '<div id="mmm-forecast">';
	
	// $forecastNode = $weather->xpath( 'forecastGroup/forecast' );
	// $forecastCount = count( $forecastNode );
	
	// loop through forecasts
	for ( $i = 0; $i < 3; $i++) {
	$wx_html .= '<h3>' . $weather->forecastGroup->forecast[$i]->period . '</h3>';
	$wx_html .= '<img src="http://ds.monasheemountainmultimedia.com/wx/icons/ec/' . $weather->forecastGroup->forecast[$i]->abbreviatedForecast->iconCode .
	'.png" class="mmm-forecast-icon" /><br />';
	$wx_html .= '<p>' . $weather->forecastGroup->forecast[$i]->textSummary . '</p>';
	}

	$wx_html .= '</div><!-- mmm-forecast -->';

	} else {
	// file not available
	$wx_html .= '<p>Weather feed currently not available.</p>';
	}

	$wx_html .= $after_widget;

	echo $wx_html;
	}
}

?>