<?php
/**
 * Plugin Name: Slider Options
 * Plugin URI: #
 * Version: 1.1.2.2
 * Author: Simon Jan
 * Author URI: https://profiles.wordpress.org/simonjan
 * Description: Manager your slider by options drag and drop. Support all most responsive jquery slider have options: bxSlider, flexSlider, nivoSlider, swiper slider,... and your custom slider
 * License: GPL3 or later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) .'includes/define.php' );

require_once( plugin_dir_path( __FILE__ ) .'includes/class.smslider.php' );

// Called when plugin active/deactive
if( ! class_exists( 'SMSD_Activator ') ){
	require( plugin_dir_path( __FILE__ ) .'includes/class.smsd-activator.php' );	
	$setting = new SMSD_Activator();
	// // activator
	register_activation_hook( __FILE__, array( &$setting, 'active') );

	// deactivator
	register_deactivation_hook( __FILE__, array( &$setting, 'deactive') );
}