<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\Template;

class OptionsField {

  private $id;

  private $label;

  private $option;

  private $options;

  private $description;

  public function __construct ( $option, $field ) {

    $this->id = $field['id'];

    $this->label = $field['label'];
    
    $this->option = $option . '[' . $field['id'] . ']';
    
    $this->options = isset( $field['options'] ) ? $field['options'] : [];

    $this->description = $field['description'];
  }

  /**
   * createCheckbox
   * 
   * Outputs checkbox field in admin options page
   * 
   * @return string
   */
  public function createCheckbox () {

    printf( 
      '<input id="' . $this->id . '" name="' . $this->id . '" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( $this->option ), false ) );

    printf( 
      '<label class="description" for="' . $this->id . '">%s</label>',
      __( $this->label ) );
  }

  /**
   * createTextField
   * 
   * Outputs text field in admin options page
   * 
   * @return string
   */
  public function createTextField () {

    $options = '';
    if ( ! empty( $this->options ) ) {

      // Wrap each option in code tag and output string
      $options = array_reduce( $this->options, function( $acc, $curr) {
        return $acc . '<code>' . $curr . '</code> ';
      }, '');
    }

    printf(
      '<input id="' . $this->id . '" class="regular-text" name="' . $this->id . '" type="text" value="%s" />',
      ( Template::getOption( $this->option ) ? Template::getOption( $this->option ) : '' ) );
  

    if ( ! empty( $this->description ) ) {
      
      echo '<p>' . __( $this->description ) . $options . '</p>';
    }
  }
}