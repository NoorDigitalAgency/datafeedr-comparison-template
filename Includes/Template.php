<?php

namespace Noor\DatafeedrExt;

class Template {

  public static $options;

  private $allNetworks;

  private $activeNetworks;

  private $activeNetworksIds;

  public function __construct( $adminFields ) {

    if ( function_exists( 'dfrapi_api_get_all_networks' ) ) {
      
      $this->activeNetworksIds = get_option('dfrapi_networks');

      $this->allNetworks = dfrapi_api_get_all_networks();
    }

    if ( is_array( $this->activeNetworksIds ) && ! empty( $this->activeNetworksIds ) ) {

      $this->activeNetworks = $this->setActiveNetworks();
    }

    $admin = new AdminOptions( $this->getActiveNetworks(), $adminFields );
    add_action( 'admin_menu', [$admin, 'templateSubMenuPage'], 999 );
    add_action( 'admin_init', [$admin, 'templateFields'] );

    $public = new PublicUI();
    add_filter( 'dfrcs_order',    [$public, 'orderDesc'], 99, 2 );
    add_filter( 'dfrcs_orderby',  [$public, 'orderBy'], 10, 2 );
    add_filter( 'dfrcs_title',    [$public, 'showTitle'], 10, 2 );
    add_filter( 'dfrcs_image',    [$public, 'showImage'], 10, 2 );
    add_filter( 'dfrcs_logo',     [$public, 'showMerchant'], 10, 2 );
    add_filter( 'dfrcs_price',    [$public, 'showPrice'], 10, 2 );
    add_filter( 'dfrcs_link',     [$public, 'networkURIExtention'], 10, 2 );
    add_filter( 'dfrcs_products', [$public, 'setNumProducts'], 10, 2 );
    add_filter( 'dfrcs_template', [$public, 'setTemplate'], 10, 2 );
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