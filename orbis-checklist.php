<?php
/*
Plugin Name: Orbis Checklist
Plugin URI: http://www.pronamic.eu/plugins/orbis-checklist/
Description: The Orbis Checklist plugin extends your Orbis environment with some checklist functionality.

Version: 1.0.0
Requires at least: 3.5

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: orbis_checklist
Domain Path: /languages/

License: Copyright (c) Pronamic

GitHub URI: https://github.com/pronamic/wp-orbis-checklist
*/

function orbis_checklist_bootstrap() {
	// Classes
	require_once 'classes/orbis-checklist-plugin.php';

	// Initialize
	global $orbis_checklist_plugin;

	$orbis_checklist_plugin = new Orbis_Checklist_Plugin( __FILE__ );
}

add_action( 'orbis_bootstrap', 'orbis_checklist_bootstrap' );
