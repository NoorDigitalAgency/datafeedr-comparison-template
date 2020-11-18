<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\Template;

class PublicUI {

  public function __construct () {

  }

  /**
   * get_network_url_extention
   * 
   * Constructs epi/sub extention to product uri
   * 
   * @param array $product
   * 
   * @return string
   */
  protected function getNetworkURIExtention ( array $product ): string {

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
   * validate_extra_args
   * 
   * @param array $args
   * 
   * @return array
   */
  protected function validateExtraArgs ( array $args ): array {

    $validArgs = [
      'display',
      'display_type',
      'display_tex'
    ];

    return array_filter( $args, function ( $arg ) {
      
      return in_array( $arg, $validArgs );
    });
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
  
    if ( false === Template::getOption( 'order_desc' ) ) {
  
      return $order;
    }
  
    return 'desc';
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
   * showTitle
   * 
   * @param string $title
   * 
   * @param Dfrcs $compset
   * 
   * @return string
   */
  public function showTitle ( string $title, \Dfrcs $compset ): string {

    if ( false === Template::getOption( 'show_title' ) ) {

      return '';
    }

    return $title;
  }

  /**
   * showImage
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function showImage ( string $html, array $product ): string {

    if ( false === Template::getOption( 'show_image' ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * showMerchant
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function showMerchant ( string $html, array $product ): string {

    if ( false === Template::getOption( 'show_merchant' ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * showPrice
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function showPrice ( string $html, array $product ): string {

    if ( false === Template::getOption( 'show_price' ) ) {
      
      return '';
    }

    return $html;
  }

  /**
   * networkURIExtention
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function networkURIextention( string $url, array $product ): string {

    if ( ! empty( $extention = $this->getNetworkURIExtention( $product ) ) ) {

      return $url . $extention;
    }

    return $url;
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

    $args = $this->validateExtraArgs( $compset->source->original );

    if ( isset( $args['display_num'] ) && absint( $args['display_num'] ) > 0 ) {

      $count = 0;
      $new_products = [];

      foreach ( $products as $product ) {

        $count++;

        if ( $count > $args['display_num'] ) {

          break;
        }

        $new_products[] = $product;
      }

      return $new_products;
    }

    return $products;
  }

  /**
   * setTemplate
   * 
   * Overrides datafeedr default template if custom filter is present
   * 
   * @param string $template
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function setTemplate ( string $template, \Dfrcs $instance ): string {

    $args = $instance->source->original;

    $template_path = plugin_dir_path( __FILE__ ) . 'templates';

    if ( isset( $args['display'] ) && ! empty( $args['display'] ) ) {

      switch( $args['display'] ) {
        case 'button' :
          $template = $template_path . '/template-button.php';
          break;
        case 'text' :
          $template = $template_path . '/template-text.php';
          break;
        default :
          $template = $template_path . '/template-default.php';
      }

      return $template;
    }

    return $template_path . '/template-default.php';
  }
}