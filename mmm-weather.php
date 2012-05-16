<?php
/*
Plugin Name: MMM Weather
Plugin URI: http://monasheemountainmultimedia.com
Description: Weather live feed from Environment Canada that displays in a page or as a widget.
Author: Derek Marcinyshyn
Version: 2.2.1
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
define( 'MMM_WX_VERSION', '2.2.1');

/** Get Directory */
define( 'MMM_WX_DIRECTORY', dirname( plugin_basename( __FILE__ ) ) );

/** Path for Includes */
define( 'MMM_WX_PATH', WP_PLUGIN_DIR . '/' . MMM_WX_DIRECTORY );

/** URL for front-end links */
define( 'MMM_WX_URL', WP_PLUGIN_URL . '/' . MMM_WX_DIRECTORY );

/** URL for icons */
define( 'MMM_WX_ICON_URL', WP_PLUGIN_URL . '/' . MMM_WX_DIRECTORY . '/images/icons/' );

/** Load Core Functions */
include_once MMM_WX_PATH . '/core/class_core.php';

/** Load Updater Class */
include_once MMM_WX_PATH . '/core/class_updater.php';

/** Load Widget Class */
include_once MMM_WX_PATH . '/core/class_widgets.php';

/** Initiate the plugin */
add_action( "after_setup_theme", create_function( '', 'new MMM_WX_Core;' ) );
