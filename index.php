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
 * Plugin updater to push updates from github to wp admin interface
 */
$plugin_updater = Puc_v4_Factory::buildUpdateChecker(
	$package->homepage,
	__FILE__,
	$package->name
);

$plugin_updater->getVcsApi()->enableReleaseAssets();

class NoorDFRCSTemplate {

  public function __construct() {

    add_action( 'admin_menu', [$this, 'register_template_sub_menu_page'], 999 );
    add_action( 'admin_init', [$this, 'register_template_settings'] );
    add_filter( 'dfrcs_template', [$this, 'load_custom_template'], 10, 2 );
  }

  /**
   * register_template_sub_menu_page
   * 
   * Registers sub menu page to Datafeedr API menu page
   * 
   * @return void
   */
  public function register_template_sub_menu_page () {

    add_submenu_page( 
      'dfrapi', 
      __( 'Datafeedr Template Options' ),
      __( 'Datafeedr Template Options' ),
      'manage_options',
      'dftemplate-settings',
      [$this, 'template_menu_page_html'],
      NULL
    );
  }

  /**
   * register_template_settings
   * 
   * Registers page options group for storing settings
   * 
   * @return void
   */
  public function register_template_settings () {
    register_setting( 
      'dftemplate_settings_group', 
      'dftemplate_settings'
    );
  }

  /**
   * template_menu_page_html
   * 
   * Callback for rendering the page markup
   * 
   * @return void
   */
  public function template_menu_page_html () {

    $options = get_option('dftemplate_settings');

    require plugin_dir_path( __FILE__ ) .'/templates/menu-page.php';
  }

  /**
   * load_custom_template
   * 
   * Overrides Datafeedr Comparison Sets default template
   * 
   * @param string $template
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function load_custom_template ( string $template, Dfrcs $instance ): string {
    
    return plugin_dir_path( __FILE__ ) . '/templates/template.php';
  }
}

if ( ! class_exists( 'Dfrcs' ) ) {

  wp_die( 'This plugin relies on datafeedr Comparison Sets plugin.' );
}

$template = new NoorDFRCSTemplate();

// add_filter( 'dfrcs_filter_products', function ( $filtered_products, $all_products ) {

//   var_dump('<pre>', isset($), '</pre>');

//   return $filtered_products;
// }, 99, 2);

// add_filter( 'dfrcs_valid_filters', function($filters) {

//   $filters[] = 'noor_custom';
//   return $filters;
// }, 99);

// add_filter( 'dfrcs_products', function( $products, $compset ) {
  
//   return $products;
// }, 99, 2);

// add_filter( 'dfrcs_arguments', function ( $args, $instance ) {

//   return $args;
// }, 99, 2);

add_filter( 'dfrcs_order', function ( $order, $instance ) {

  $tmpl_options = get_option('dftemplate_settings');

  if ( $tmpl_options['from_highest'] === '1' ) {

    return 'desc';
  }

  return $order;
}, 99, 2);

add_filter( 'dfrcs_orderby', function( $orderby, $instance ) {
  $tmpl_options = get_option('dftemplate_settings');

  return $orderby;
}, 10, 2);