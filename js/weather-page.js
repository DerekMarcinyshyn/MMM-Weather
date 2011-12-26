/**
 * MMM Weather Page jQuery
 */

jQuery( document ).ready( function() {
	jQuery( "#mmm-weather-page-loader" ).show();
	
	// load initial weather conditions
	jQuery.ajax( {
		type : 'POST',
		url : MMMWeather.ajaxurl,
		data : 	{
			action : 'mmmweather_submit',
			location : 'revelstoke'
			},
		success : function( data ) {
			jQuery( "#mmm-weather-page-container" ).slideUp();
			jQuery( "#mmm-weather-page-container" ).html( data.display );
			jQuery( "#mmm-weather-page-loader" ).hide();
			jQuery( "#mmm-weather-page-container" ).slideDown( 'slow' );
		}
	});	
	
	// wait for drop down selection change
	jQuery( "#wx-location" ).change( function() {
		jQuery( "#mmm-weather-page-loader" ).show();
		
		jQuery( "#mmm-weather-page-container" ).fadeOut( 1600 );
		
		jQuery.ajax( {
			type : 'POST',
			url : MMMWeather.ajaxurl,
			data : 	{
				action : 'mmmweather_submit',
				location : jQuery( "#wx-location option:selected" ).val()
				},
			success : function( data ) {
				jQuery( "#mmm-weather-page-container" ).empty();
				jQuery( "#mmm-weather-page-container" ).html( data.display );
				jQuery( "#mmm-weather-page-loader" ).hide();
				jQuery( "#mmm-weather-page-container" ).fadeIn( 1600 );
			}
		});
	});
});