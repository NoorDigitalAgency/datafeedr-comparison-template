<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\{DfrExtention, Options};

class Template extends DfrExtention {

  public function __construct() {
    
    $this->dependencies();
  }

  protected function dependencies () {

    new Options();

    add_filter( 'dfrcs_order',    [$this, 'orderDesc'], 99, 2 );
    add_filter( 'dfrcs_orderby',  [$this, 'orderBy'], 10, 2 );
    add_filter( 'dfrcs_link',     [$this, 'uriExtention'], 10, 2 );
    add_filter( 'dfrcs_products', [$this, 'products'], 10, 2 );
    add_filter( 'dfrcs_template', [$this, 'template'], 10, 2 );
  }

  /**
   * orderDesc
   * 
   * Sets products order to either asc|desc
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function orderDesc ( string $order, \Dfrcs $instance ): string {

    return ( false === $this->getOption( 'order_desc' ) ) 
      ? $order
      : 'desc';
  }

  /**
   * orderBy
   * 
   * Sets products value to order by
   * 
   * @param string $order
   * 
   * @param Dfrcs $instance
   * 
   * @return string
   */
  public function orderBy ( string $orderby, \Dfrcs  $instance ): string {
    
    return ( ! empty( $this->getOption( 'order_by' ) ) ) 
      ? $this->getOption( 'order_by' )
      : $orderby;
  }

  /**
   * uriExtention
   * 
   * Sets a uri extention to product
   * 
   * @param string $title
   * 
   * @param array $product
   * 
   * @return string
   */
  public function uriExtention( string $url, array $product ): string {

    return ( ! empty( $extention = $this->getURIExtention( $product ) ) ) 
      ? $url . $extention
      : $url;
  }

  /**
   * filterProducts
   * 
   * Sets products to render
   * 
   * @param array $products
   * 
   * @param Dfrcs $compset
   * 
   * @return array
   */
  public function products ( array $products, \Dfrcs $compset ): array {

    return $this->getProducts( $products, $compset->source->original );
  }

  /**
   * template
   * 
   * Overrides datafeedr default template if custom filter is present
   * 
   * @param string $template
   * 
   * @param Dfrcs $compset
   * 
   * @return string
   */
  public function template ( string $template, \Dfrcs $compset ): string {
    
    $template = $this->getTemplate( $compset->source->original );
  
    return plugin_dir_path( __FILE__ ) . "../templates/template-{$template}.php";
  }
}