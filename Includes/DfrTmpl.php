<?php

namespace Noor\DatafeedrExt;

abstract class DfrTmpl {

  /**
   * dependencies
   * 
   * loads component dependencies
   * 
   * @return void
   */
  protected abstract function dependencies ();

  /**
   * setDisplayMethod
   * 
   * Ensures datafeedr comparison sets is run with display mode php
   * 
   * @return void
   */
  protected function setDisplayMethod () {
    
    $dfrcs_options = get_option('dfrcs_options');

    if ( $dfrcs_options && $dfrcs_options['display_method'] != 'php' ) {

      $dfrcs_options['display_method'] = 'php';

      update_option( 'dfrcs_options', $dfrcs_options ); 
    }
  }

  /**
   * getOption
   * 
   * @param string $key
   * 
   * @return mixed
   */
  public static function getOption ( string $key ) {

    $options = get_option( 'tmpl_options', [
      'show_title'    => 1,
      'show_image'    => 1,
      'show_merchant' => 1,
      'show_price'    => 1,
      'order_desc'    => 0,
      'order_by'      => '',
    ]);
    
    return ( isset( $options[$key] ) ? $options[$key] : false );
  }

  /**
   * getArgs
   * 
   * filters Dfrcs_Source original source where custom filters are available
   * 
   * @param array $args
   * 
   * @return array
   */
  public static function getArgs ( array $args ): array {
    
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
   * getTemplate
   * 
   * Decide wich template to render based on the display filter
   * 
   * @param array $args
   * 
   * @return string
   */
  protected function getTemplate ( array $args ): string {

    $this->getArgs( $args );

    if ( isset( $args['display'] ) && ! empty( $args['display'] ) ) {
      
      switch ( $args['display'] ) {
        case 'text' :
          $template = 'text';
          break;
        case 'button' :
          $template = 'button';
          break;
        default :
          $template = 'default';
      }

      return $template;
    }

    return 'default';
  }

  /**
   * getActiveNetworks
   * 
   * Generates array of active networks
   * 
   * @return array
   */
  protected function getActiveNetworks (): array {

    if ( function_exists( 'dfrapi_api_get_all_networks' ) ) {
    
      $activeNetworks = array_keys( get_option('dfrapi_networks')['ids'] );
      
      return array_filter( dfrapi_api_get_all_networks(), function ( $network ) use ( $activeNetworks ) {
          
        return in_array( $network['_id'], $activeNetworks );
      });
    }
  }

  /**
   * getProducts
   * 
   * Intercepts dfrcs_products call and applies display_num filter
   * 
   * @param array $products
   * 
   * @param array $args
   * 
   * @return array
   */
  protected function getProducts ( array $products, array $args ): array {

    $this->getArgs( $args );

    if ( isset( $args['display_num'] ) && absint( $args['display_num'] ) > 0 ) {

      return array_reduce( array_keys( $products ), function( $acc, $curr ) use ( $products, $args ) {
        
        if ( $args['display_num'] > count( $acc ) ) {

          $acc[$curr] = $products[$curr];
        }

        return $acc;
      }, []);
    }

    return $products;
  }

  /**
   * getURIExtention
   * 
   * Constructs epi/sub extention to product uri
   * 
   * @param array $product
   * 
   * @return string
   */
  protected function getURIExtention ( array $product ): string {

    if ( empty( $extention = $this->getOption( 'uri_ext_' . $product['source_id'] ) ) ) {

      return '';
    }

    preg_match_all( "/\\{(.*?)\\}/", $extention, $matches, PREG_PATTERN_ORDER );
    
    foreach ( $matches[0] as $tag ) {

      switch( $tag ) {
        case '{page}' :
          $extention = str_replace( $tag, get_post()->post_name, $extention );
          break;
        case '{product}' :
          $extention = str_replace( $tag, urlencode( $product['name'] ), $extention );
          break;
        case '{price}' :
          $extention = str_replace( $tag, $product['price'], $extention );
          break;
        default :
          $extention = str_replace( $tag, $product['finalprice'], $extention );
      }
    }
    
    return '&' . $extention;
  }
}