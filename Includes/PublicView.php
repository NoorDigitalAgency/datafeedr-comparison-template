<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\Template;

class PublicView {

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
  private function getNetworkURIExtention ( array $product ): string {

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
  private function validateExtraArgs ( array $args ): array {

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
    
    $args = $instance->source->original;

    return plugin_dir_path( __FILE__ ) . 'templates/template-default.php';
  }
}

// $html = '';

//     ob_start();

//     global $compset;
//     if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) {

//       $html .= sprintf( '<h2>%s</h2>', dfrcs_title() );

//       if ( $dfrcs_products = dfrcs_products() ) {

//         global $dfrcs_product;

//         $row = '<li class="' . dfrcs_row_class() . '">';
//         foreach ( $dfrcs_products as $dfrcs_product ) {

//           $image = ( false != Template::getOption( 'show_image' )
//             ? '<div class="dfrcs_image">' . dfrcs_image() . '</div>'
//             : '' );
          
//           $merchant = ( false != Template::getOption( 'show_merchant' )
//             ? '<div class="dfrcs_logo">' . dfrcs_logo() . '</div>'
//             : '' );

//           $price = ( Template::getOption( 'show_price' )
//             ? '<div class="dfrcs_price">' . dfrcs_price() . '</div>'
//             : '' );

//           $link = '<div class="dfrcs_link"><span class="dfrcs_action">' . dfrcs_link_text() . '</span></div>';
          
//           $item = sprintf( 
//             '<div class="item">%1$s %$2s %3$s %4$s</div>',
//             $image,
//             $merchant,
//             $price,
//             $link );
          
//           $row .= sprintf( 
//             '<a target="_blank" href="%1$s" rel="nofollow">%2$ s%3$s</a>%4$ s%5$s',
//             dfrcs_url(),
//             $item,
//             dfrcs_promo(), 
//             dfrcs_product_actions(),
//             dfrcs_product_debug() );
//         }

//         $row .= '</li>';
//       }
//     }

//     $html .= ob_get_contents();
//   	ob_end_clean();

//     return $html;