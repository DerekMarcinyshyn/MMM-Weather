<?php
/**
 * MMM Weather Widget Class
 * 
 * @author Derek Marcinyshyn
 * @package MMM Weather
 * @subpackage Widget
 * @version 2.2.0
 * 
 */
if(!class_exists('MMM_Weather')) {
	
	class MMM_Weather extends WP_Widget
	{
		/**
		 * MMM Weather
		 *
		 * Registers the widget details with the parent class
		 */
		public function MMM_Weather()
		{
			// widget actual processes
			parent::WP_Widget( $id = 'mmm_weather', $name = get_class($this), $options = array( 'description' => 'MMM Weather' ) );
		}
		
		/** Outputs the options form in admin */
		
		function form($instance)
		{
			?>
			No config.
			<?php
		}
		
		/** Handles any special functions when the widget is being updated. */
	
		function update($new_instance, $old_instance)
		{
			// processes widget options to be saved
			$instance = wp_parse_args($old_instance, $new_instance);
			$instance['title'] = 'Revelstoke Weather';
			return $instance;
		}
		
		/** Renders the widget on the front-end. */
	
		function widget($args, $instance)
		{
			/* outputs the content of the widget */
			extract( $args );
			
			$wx_html =  $before_widget;
			
			/* Title of Widget in Siderbar */
			$wx_html .= '<h3 class="widget-title">Revelstoke Weather</h3>';
			
			/* load Environment Canada XML for Revelstoke, BC */
			$weather_url = 'http://dd.weatheroffice.gc.ca/citypage_weather/xml/BC/s0000679_e.xml';
			$AgetHeaders = @get_headers( $weather_url );
			
			/* check if file exists */
			if ( preg_match( "|200|", $AgetHeaders[0] ) ) {
			$weather = @simplexml_load_file($weather_url);
			
			/* ouput weather to screen */
			$wx_html .= '<div id="mmm-date">' . $weather->dateTime[1]->textSummary . '</div>';
			if ( $weather->currentConditions->iconCode ) {
				$wx_html .= '<div><img src="' . MMM_WX_ICON_URL . $weather->currentConditions->iconCode . '.png" class="mmm-weather-icon" />';
			}
			$wx_html .= '<div id="mmm-temperature">' . $weather->currentConditions->temperature . '&deg;C</div>';
			$wx_html .= '<div id="mmm-sky">' . $weather->currentConditions->condition . '</div></div>';
			$wx_html .= '<div id="mmm-more"><a>Click for Forecast</a></div>';
			$wx_html .= '<div id="mmm-forecast">';
						
			/* loop through forecasts */
			for ( $i = 0; $i < 3; $i++) {
				$wx_html .= '<h3 class="widget-title">' . $weather->forecastGroup->forecast[$i]->period . '</h3>';
				
				if ( $weather->forecastGroup->forecast[$i]->abbreviatedForecast->iconCode ) {
					$wx_html .= '<img src="' . MMM_WX_ICON_URL . $weather->forecastGroup->forecast[$i]->abbreviatedForecast->iconCode .
					'.png" class="mmm-forecast-icon" /><br />';
				}
				
				$wx_html .= '<p>' . $weather->forecastGroup->forecast[$i]->textSummary . '</p>';
			}
		
			$wx_html .= '</div><!-- mmm-forecast -->';
		
			} else {
				
			/* file not available */
			$wx_html .= '<p>Weather feed currently not available.</p>';
			}
		
			$wx_html .= $after_widget;
		
			echo $wx_html;
			}
		}
}