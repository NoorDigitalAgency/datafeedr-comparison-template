<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\Template;

class PublicView {

  public function __construct () {}

  /**
   * getProductURIExtention
   * 
   * Constructs epi/sub extention to product uri
   * 
   * @param array $product
   * 
   * @return string
   */
  private function getProductURIExtention ( array $product ): string {

    if ( empty( $extention = Template::getOption( 'uri_ext_' . $product['source_id'] ) ) ) {

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

  /**
   * orderDesc
   * 
   * Sort order either asc|desc
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function orderDesc ( string $order, \Dfrcs $instance ): string {
    var_dump('<pre>', Template::getOption( 'order_desc' ), '</pre>');
    return ( false === Template::getOption( 'order_desc' ) ) 
      ? $order
      : 'desc';
  }

  /**
   * orderBy
   * 
   * Sort order price or mrechant
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function orderBy ( string $orderby, \Dfrcs  $instance ): string {
    
    return ( ! empty( Template::getOption( 'order_by' ) ) ) 
      ? Template::getOption( 'order_by' )
      : $orderby;
  }

  /**
   * productURIExtention
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function productURIextention( string $url, array $product ): string {

    return ( ! empty( $extention = $this->getProductURIExtention( $product ) ) ) 
      ? $url . $extention
      : $url;
  }

  /**
   * setNumProducts
   * 
   * Controls product num outputs
   * 
   * @param array $products
   * 
   * @param Dfrcs $compset
   * 
   * @return array
   */
  public function setNumProducts ( array $products, \Dfrcs $compset ): array {

    Template::validateArgs( $args = $compset->source->original );

    if ( isset( $args['display_num'] ) && absint( $args['display_num'] ) > 0 ) {

      return array_reduce( array_keys( $products ), function( $acc, $curr ) use ( $products, $args ) {
        
        if ( $args['display_num'] > count( $acc ) ) {

          $acc[$curr] = $products[$curr];
        }

        return $acc;
      }, [] );
    }

    return $products;
  }

  /**
   * template
   * 
   * Overrides datafeedr default template if custom filter is present
   * 
   * @param string $template
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function template ( string $template, \Dfrcs $instance ): string {
    
    Template::validateArgs( $args = $instance->source->original );
      
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

    return plugin_dir_path( __FILE__ ) . "/../templates/template-{$template}.php";
  }
}
