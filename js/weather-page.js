jQuery(document).ready(function() {
	jQuery("#mmm-weather-page-loader").show();
	
	jQuery.ajax({
		type : 'POST',
		url : MMMWeather.ajaxurl,
		data : 	{
			action : 'mmmweather_submit',
			location : 'revelstoke'
			},
		success : function(data) {
			jQuery("#mmm-weather-page-container").slideUp();
			jQuery("#mmm-weather-page-container").html(data.display);
			jQuery("#mmm-weather-page-loader").hide();
			jQuery("#mmm-weather-page-container").slideDown('slow');
		}
	});	
			
	jQuery("#wx-location").change(function() {
		jQuery("#mmm-weather-page-loader").show();
		
		jQuery("#mmm-weather-page-container").fadeOut(1600);
		
		jQuery.ajax({
			type : 'POST',
			url : MMMWeather.ajaxurl,
			data : 	{
				action : 'mmmweather_submit',
				location : jQuery("#wx-location option:selected").val()
				},
			success : function(data) {
				jQuery("#mmm-weather-page-container").empty();
				jQuery("#mmm-weather-page-container").html(data.display);
				jQuery("#mmm-weather-page-loader").hide();
				jQuery("#mmm-weather-page-container").fadeIn(1600);
			}
		});
	});
});