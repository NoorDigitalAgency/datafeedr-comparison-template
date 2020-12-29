<?php
/**
 * Plugin Name: Datafeedr Comparison Template
 * Description: Template extention options to datafeedr Comparison Sets
 * Version: 1.1.0
 * Author: Noor Digital Agency
 * Author URI: https://noordigital.com 
 */

if ( ! class_exists( 'Dfrapi' ) && ! class_exists( 'Dfrcs' ) ) {

//   wp_die( 'This plugin relies on datafeedr Comparison Sets plugin.' );
}

if ( ! file_exists( $autoload = __DIR__ . '/vendor/autoload.php' ) ) {

//   wp_die( __( 'This plugin requires Composer.' ) );
}

require $autoload;

$package = json_decode( file_get_contents( __DIR__ . '/composer.json' ), false );

$plugin_updater = \Puc_v4_Factory::buildUpdateChecker( $package->homepage, __FILE__, $package->name );

$plugin_updater->getVcsApi()->enableReleaseAssets();

new Noor\DatafeedrExt\Template();
