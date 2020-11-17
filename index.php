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
  
  private $post;

  private $options;

  private $all_networks;

  private $active_networks;

  private $active_network_ids;

  public function __construct() {
    
    // Makesure to set display type to php in order to have post params available
    $dfrcs_options = get_option('dfrcs_options');
    if ( $dfrcs_options && $dfrcs_options['display_method'] != 'php' ) {

      $dfrcs_options['display_method'] = 'php';

      update_option( 'dfrcs_options', $dfrcs_options ); 
    }

    add_action( 'admin_menu', [$this, 'register_template_sub_menu_page'], 999 );
    add_action( 'admin_init', [$this, 'register_template_settings'] );
    add_filter( 'dfrcs_order', [$this, 'sort_desc'], 99, 2 );
    add_filter( 'dfrcs_orderby', [$this, 'order_by'], 10, 2 );
    add_filter( 'dfrcs_title', [$this, 'show_title'], 10, 2 );
    add_filter( 'dfrcs_image', [$this, 'show_product_image'], 10, 2 );
    add_filter( 'dfrcs_logo', [$this, 'show_merchant'], 10, 2 );
    add_filter( 'dfrcs_price', [$this, 'show_price'], 10, 2 );
    add_filter( 'dfrcs_link', [$this, 'network_uri_extention'], 10, 2 );

    $this->options = get_option('dftemplate_settings');
    
    if ( function_exists( 'dfrapi_api_get_all_networks' ) ) {
      
      $this->active_network_ids = get_option('dfrapi_networks');

      $this->all_networks = dfrapi_api_get_all_networks();
    }

    if ( is_array( $this->active_network_ids ) && ! empty( $this->active_network_ids ) ) {

      $this->get_active_networks();
    }
  }
  
  /**
   * get_active_networks
   * 
   * Generates array of active networks
   * 
   * @return void
   */
  private function get_active_networks (): void {

    if ( function_exists( 'dfrapi_api_get_all_networks' ) ) {

      $active_networks = array_keys( $this->active_network_ids['ids'] );
      
      $this->active_networks = array_filter( $this->all_networks, function ( $network ) use ( $active_networks ) {
        
        return in_array( $network['_id'], $active_networks );
      });
    }
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
      __( 'Template Options' ),
      __( 'Template Options' ),
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

    $options = $this->options;
    $active_networks = $this->active_networks;

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
  // public function load_custom_template ( string $template, Dfrcs $instance ): string {
    
  //   return plugin_dir_path( __FILE__ ) . '/templates/template.php';
  // }

  /**
   * sort_desc
   * 
   * Sort order either asc|desc
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function sort_desc ( string $order, Dfrcs $instance ): string {
  
    if ( isset( $this->options['sort_desc'] ) && 1 == $this->options['sort_desc'] ) {
  
      return 'desc';
    }
  
    return $order;
  }

  /**
   * order_by
   * 
   * Sort order price or mrechant
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function order_by ( string $orderby, Dfrcs  $instance ): string {
    
    if ( isset( $this->options['order_by'] ) && ! empty( $this->options['order_by'] ) ) {

      return $this->options['order_by'];
    }
  
    return $orderby;
  }

  /**
   * show_title
   * 
   * @param string $title
   * 
   * @param Dfrcs $compset
   * 
   * @return string
   */
  public function show_title ( string $title, Dfrcs $compset ): string {

    if ( ! isset( $this->options['show_title'] ) ) {

      return '';
    }

    return $title;
  }

  /**
   * show_product_image
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function show_product_image ( string $html, array $product ): string {

    if ( ! isset( $this->options['show_prod_img'] ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * show_merchant
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function show_merchant ( string $html, array $product ): string {

    if ( ! isset( $this->options['show_merchant'] ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * show_price
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function show_price ( string $html, array $product ): string {

    if ( ! isset( $this->options['show_price'] ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * network_uri_extention
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function network_uri_extention( string $url, array $product ): string {

    if ( empty( $extention = $this->options['uri_ext_' . $product['source_id']] ) ) {

      return $url;
    }

    $post = get_post()->post_name;

    $extention = str_replace( '{page}', $post, $extention );

    $extention = str_replace( '{product}', urlencode($product['name']), $extention );
    
    return $url . '&' . $extention;
  }
}

if ( ! class_exists( 'Dfrapi' ) && ! class_exists( 'Dfrcs' ) ) {

  wp_die( 'This plugin relies on datafeedr Comparison Sets plugin.' );
}

$template = new NoorDFRCSTemplate();
