/**
 * MMM Weather Page jQuery
 */

jQuery( document ).ready( function() {
	jQuery( "#weather-tabs").tabs();
		
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
		
		jQuery( "#mmm-weather-page-container" ).fadeOut( 600 );
		
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
				jQuery( "#mmm-weather-page-container" ).fadeIn( 1200 );
			}
		});
	});
	
	// set up infrared fancybox
	jQuery( "#fb-infrared" ).fancybox({
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'overlayColor'		: '#333',
		'speedIn'			: '450',
		'speedOut'			: '450'
	});

	// set up jet stream fancybox
	jQuery( "#fb-jet" ).fancybox({
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'overlayColor'		: '#333',
		'speedIn'			: '450',
		'speedOut'			: '450'
	});
	
	// set up 48 Hour Fronts
	jQuery( "#fb-fronts" ).fancybox({
		'type'				: 'iframe',
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'width'				: '95%',
		'height'			: '95%'
	});
	
	// set up 7 Day Forecast
	jQuery( "#fb-seven" ).fancybox({
		'type'				: 'iframe',
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'width'				: '95%',
		'height'			: '95%'
	});
});