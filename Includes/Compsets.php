<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\DfrExtention;

class Compsets extends DfrExtention {

  private $compsets;

  public function __construct () {

    if ( defined( 'DFRCS_TABLE' ) ) {

      $this->get();
    }

    return $this;
  }

  private function groupSets ( array $dbresult ): array {

    return array_reduce( $dbresult, function( $acc, $curr ) use ( $dbresult ) {
      
      $log = maybe_unserialize( $curr['log'] );
        
      $page = get_the_title( $log['original_source']['post_id'] );

      $acc[$page][] = $curr;
  
      return $acc;
        
    }, []);
  }

  private function update ( string $id, array $log ) {

    global $wpdb;
  
    $table = $wpdb->prefix . DFRCS_TABLE;
  
    $wpdb->update(
      $table,
      [
        'log' => serialize( $log )
      ],
      // [
      //    'hash' => $this->source->hash 
      // ],
      [
        'id' => $id
      ]
      // [
      //   '%s'
      // ]
    );
  }
  
  private function get () {

    global $wpdb;

    $table = $wpdb->prefix . DFRCS_TABLE;
  
    $result = $wpdb->get_results("SELECT * FROM $table", ARRAY_A );

    $this->compsets = $this->groupSets( $result );
  }
  
  protected function dependencies () {}

  public function getCompsets () {

    return $this->compsets;
  }

  public function updateCompset ( $set ) {

    if ( $set['id'] === '4' ) {

      $log = maybe_unserialize( $set['log'] );
      
      $log['original_source']['display'] = 'button';

      $this->update( $set['id'], $log );
    }
  }

  public function getOriginalSource ( array $compset ): array {

    return maybe_unserialize( $compset['log'] )['original_source'];
  }
}