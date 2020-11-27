<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\DfrExtention;

class AdminAlert extends DfrExtention {

  public function __construct () {

    $this->dependencies();
  }

  protected function dependencies () {

    add_filter( 'dfrcs_products', [$this, 'log'], 99, 2 );
    add_filter( 'dfrcs_no_results_message', [$this, 'log'], 99, 2 );
    add_filter( 'add_menu_classes', [$this, 'adminAlert'], 999 );
  }

  /**
   * setLog
   * 
   * Saves the log in options table
   * 
   * @param int $id
   * 
   * @param string $timestamp
   * 
   * @param bool $increment
   * 
   * @return void
   */
  private function setLog ( $id, string $timestamp, bool $increment ) {

    $options = get_option( 'tmpl_options', DfrExtention::$defaultOptions );

    if ( $increment && in_array( $timestamp, $options['log'][$id] ) ) {

      return;
    }

    if ( ! $increment ) {

      if ( empty( $options['log'][$id] ) ) {

        unset( $options['log'][$id] );
      } else {

        $index = array_search( $timestamp, $options['log'][$id] );
        
        unset( $options['log'][$id][$index] );
      }
    } else {

      $options['log'][$id][] = $timestamp;
    }

    update_option( 'tmpl_options', $options );
  }

  /**
   * log
   * 
   * Adds an admin alert if a product set is not displaying products
   * 
   * @param string|array $maybeProducts
   * 
   * @param Dfrcs $compset
   * 
   * @return string|array
   */
  public function log ( $maybeProducts, \Dfrcs $compset ) {
    
    $increment = true;

    if ( is_array( $maybeProducts ) && dfrcs_get_option( 'minimum_num_products' ) <= count( $maybeProducts ) ) {
       
      $increment = ! $increment;
    }

    $this->setLog( 
      $compset->source->original['post_id'], 
      $compset->date_created, 
      $increment 
    );

    return $maybeProducts;
  }

  /**
   * adminAlert
   * 
   * @param array $menu
   * 
   * @return array
   */
  public function adminAlert ( array $menu ): array {

    $dfrMenu = array_filter( $menu, function ( $item ) {

      $found = false;
      foreach ( $item as $settings ) {

        if ( 'datafeedr api' === strtolower( $settings ) ) {

          $found = true;
          break;
        }
      }

      return $found === true;
    });

    if ( is_array( $log = $this->getWarnings() ) ) {
      
      $count = count( $log );

      $key = array_keys( $dfrMenu );

      $index = end( $key );
      
      $menu[$index][0] .= ' <span class="update-plugins count-' . $count . '"><span class="plugin-count">' . (string) $count . '</span></span>';
    }
    
    return $menu;
  }
}