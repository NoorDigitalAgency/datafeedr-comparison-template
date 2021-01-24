<?php
/**
 * Plugin Name: Datafeedr Comparison Template
 * Description: Template extention options to datafeedr Comparison Sets
 * Version: 1.2.0
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

/**
 * Enqueue plugin assets
 */
add_action( 'admin_enqueue_scripts', function () {

  wp_enqueue_style( 'dfrcs_template_admin_styles', plugin_dir_url( __FILE__ ) . 'assets/style-admin.css', [], '1.0.0' );
});

add_action( 'wp_enqueue_scripts', function () {

  wp_enqueue_style( 'dfrcs_template_styles', plugin_dir_url( __FILE__ ) . 'assets/style.css', [], '1.0.0' );
});