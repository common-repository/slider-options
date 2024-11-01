<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'SMSD_RQ_WP_V', '3.5' );

define( 'SMSD_V', '1.1.2.2' );

define( 'SMSD_PLUGIN', dirname( __FILE__ ) );

define( 'SMSD_PLUGIN_DIR', plugin_dir_path( SMSD_PLUGIN ) );

define( 'SMSD_PLUGIN_URL', plugin_dir_url( SMSD_PLUGIN ) );

define( 'SMSD_ASSETS_URL', SMSD_PLUGIN_URL . 'assets/' );

define( 'SMSD_ASSETS_DIR', SMSD_PLUGIN_DIR .'/assets/' );

define( 'SMSD_ADMIN_URL', admin_url('admin.php?page=sm-slider') );

define( 'SMSD_LIVE', true );

if( ! function_exists( 'smsd_init_required') ){

	function smsd_init_required( $src = 'includes', $inc = null ){
		if( empty( $inc ) ){
			$incs = array( "class.smsd-detail","class.smslider-public", "func.smslider-helper" );
			foreach( $incs as $inc ){
				require_once( SMSD_PLUGIN_DIR ."includes/{$inc}.php" );
			}
		}else{
			require( SMSD_PLUGIN_DIR . $src ."/class.smsd-{$inc}.php" );
		}
	}
}