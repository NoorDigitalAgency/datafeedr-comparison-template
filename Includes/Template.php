<?php

namespace Noor\DatafeedrExt;

class Template {

  private static $options;

  private $allNetworks;

  private $activeNetworks;

  private $activeNetworksIds;

  public function __construct() {

    if ( function_exists( 'dfrapi_api_get_all_networks' ) ) {
      
      $this->activeNetworksIds = get_option('dfrapi_networks');

      $this->allNetworks = dfrapi_api_get_all_networks();
    }

    if ( is_array( $this->activeNetworksIds ) && ! empty( $this->activeNetworksIds ) ) {

      $this->activeNetworks = $this->setActiveNetworks();
    }

    $admin = new Options( $this->getActiveNetworks() );
    add_action( 'admin_menu', [$admin, 'templateSubMenuPage'], 999 );
    add_action( 'admin_init', [$admin, 'templateOptions'] );

    $public = new PublicView();
    add_filter( 'dfrcs_order',    [$public, 'orderDesc'], 99, 2 );
    add_filter( 'dfrcs_orderby',  [$public, 'orderBy'], 10, 2 );
    add_filter( 'dfrcs_link',     [$public, 'productURIExtention'], 10, 2 );
    add_filter( 'dfrcs_products', [$public, 'setNumProducts'], 10, 2 );
    add_filter( 'dfrcs_template', [$public, 'template'], 10, 2 );
  }

  /**
   * setActiveNetworks
   * 
   * Generates array of active networks
   * 
   * @return array
   */
  private function setActiveNetworks (): array {

    $activeNetworks = array_keys( $this->activeNetworksIds['ids'] );
      
    return array_filter( $this->allNetworks, function ( $network ) use ( $activeNetworks ) {
        
      return in_array( $network['_id'], $activeNetworks );
    });
  }

  /**
   * getActiveNetworks
   * 
   * @return array
   */
  public function getActiveNetworks (): array {

    return $this->activeNetworks;
  }

  /**
   * validateArgs
   * 
   * @param array $args
   * 
   * @return array
   */
  public static function validateArgs ( array $args ): array {

    $validArgs = [
      'display',
      'display_type',
      'display_tex'
    ];

    return array_filter( $args, function ( $arg ) use ( $validArgs ) {
      
      return in_array( $arg, $validArgs );
    });
  }

  /**
   * getOption
   * 
   * @param string $key
   * 
   * @return mixed
   */
  public static function getOption ( string $key ) {

    if ( null === self::$options ) {

      self::$options = get_option( 'tmpl_options' );
    }

    return ( isset( self::$options[$key] ) ? self::$options[$key] : false );
  }
}