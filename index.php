<?php
/**
 * Plugin Name: Datafeedr Comparison Template
 * Description: Template extention options to datafeedr Comparison Sets
 * Version: 0.0.1
 * Author: Noor Digital Agency
 * Author URI: https://noordigital.com 
 */

// Require composer autoloader
if ( ! file_exists( $autoload = plugin_dir_path( __FILE__ ) .'vendor/autoload.php' ) ) {

  wp_die( __( 'This plugin requires Composer.' ) );
}

require $autoload;

$package = json_decode( file_get_contents( __DIR__ . '/composer.json' ), false );

/**
 * Plugin updater
 */
$plugin_updater = Puc_v4_Factory::buildUpdateChecker(
	$package->homepage,
	__FILE__,
	$package->name
);

$plugin_updater->getVcsApi()->enableReleaseAssets();


if ( ! class_exists( 'Dfrapi' ) && ! class_exists( 'Dfrcs' ) ) {

  wp_die( 'This plugin relies on datafeedr Comparison Sets plugin.' );
}

$template = new Noor\DatafeedrExt\Template();
