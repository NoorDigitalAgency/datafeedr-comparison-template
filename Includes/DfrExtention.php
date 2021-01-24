<?php

namespace Noor\DatafeedrExt;

abstract class DfrExtention {

  protected static $defaultOptions = [
    'show_title'    => 1,
    'show_image'    => 1,
    'show_merchant' => 1,
    'show_name'     => 1,
    'show_price'    => 1,
    'order_desc'    => 0,
    'order_by'      => '',
    'log'           => []
  ];

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

    $options = get_option( 'tmpl_options', self::$defaultOptions );
    
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
      'display_num',
      'display_at_position',
      'display_text',
      'display_class',
      'display_styles'
    ];

    return array_filter( $args, function ( $arg ) use ( $validArgs ) {
      
      return in_array( $arg, $validArgs );
    });
  }

  /**
   * getStyles
   * 
   * parse style arg input and returns a style string
   * 
   * @param array $args
   * 
   * @return string
   */
  public static function getStyles ( array $args ): string {

    if ( isset( $args['display_styles'] ) && ! empty( $args['display_styles'] ) ) {

      $styles = explode( ',', $args['display_styles'] );

      return implode( ';', $styles );
    }

    return '';
  }

  /**
   * getClassName
   * 
   * returns the args classname
   * 
   * @param array $args
   * 
   * @return string
   */
  public static function getClassName ( array $args ): string {

    return isset( $args['display_class'] ) && ! empty( $args['display_class'] ) 
      ? $args['display_class']
      : '';
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
        case 'card' :
          $template = 'card';
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
   * getWarnings
   * 
   * Looks at log option and constructs an array with unique entries
   * 
   * @return array|string
   */
  protected function getWarnings () {

    if ( $log = $this->getOption( 'log' ) ) {

      return array_map( function( $id ) use ( $log ) {

        return [
          'page_id' => $id,
          'warnings' => count( $log[$id] ),
          'permalink' => get_the_permalink( $id ),
          'editlink'  => wp_nonce_url( get_edit_post_link( $id ), 'edit' )
        ];
      }, array_keys( $log ) ); 
    }

    return 'No warnings';
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
      
      if ( $networks = get_option( 'dfrapi_networks' ) ) {

        $activeNetworks = array_keys( $networks['ids'] );
        
        return array_filter( dfrapi_api_get_all_networks(), function ( $network ) use ( $activeNetworks ) {
            
          return in_array( $network['_id'], $activeNetworks );
        });
      }
    }
    return [];
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

    if ( isset( $args['display_at_position'] ) && ! empty( $args['display_at_position'] ) ) {
      
      $products = array_filter( $products, function( $productId ) use ( $products, $args ) {
        
        $filter = explode( ',', $args['display_at_position'] );
        $index = array_search( $productId, array_keys( $products ) );

        return in_array( $index + 1, array_keys( $filter ) );
      }, ARRAY_FILTER_USE_KEY );
    }

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