<?php
/*
Plugin Name: MMM Weather
Plugin URI: http://monasheemountainmultimedia.com
Description: Weather live feed from Environment Canada that displays in a page or as a widget.
Author: Derek Marcinyshyn
Version: 1.0.0
Author URI: http://monasheemountainmultimedia.com
Network: true

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
/** Exit if called directly */
defined( 'ABSPATH' ) or die( "Cannot access pages directly." );
defined( "DS" ) or define( "DS", DIRECTORY_SEPARATOR );

/** This Version */
define( 'MMMW_VERSION', '1.0.0');

/** Get Directory */
define( 'MMMW_DIRECTORY', dirname( plugin_basename( __FILE__ ) ) );

/** Path for Includes */
define( 'MMMW_PATH', WP_PLUGIN_DIR . '/' . MMMW_DIRECTORY );

/** URL for front-end links */
define( 'MMMW_URL', WP_PLUGIN_URL . '/' . MMMW_DIRECTORY );

/** URL for icons */
define( 'MMMW_ICON_URL', WP_PLUGIN_URL . '/' . MMMW_DIRECTORY . '/images/icons/');

/** Load Core Functions */
include_once MMMW_PATH . '/core/class_core.php';

/** Load Updater Class */
include_once MMMW_PATH . '/core/class_updater.php';

/** Load Widget Class */
include_once MMMW_PATH . '/core/class_widgets.php';

/** Initiate the plugin */
add_action( "after_setup_theme", create_function( '', 'new MMMW_Core;' ) );
?>