/**
* MMM Revelstoke Weather
* scripts.js
*/

jQuery.noConflict();

jQuery(document).ready(function() {
	jQuery("#mmm-more").click(function() {
		jQuery("#mmm-forecast").toggle('slow');
	});
});

