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
      '<input id="' . $this->option . '" name="' . $this->option . '" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( $this->id ), false ) );

    printf( 
      '<label class="description" for="' . $this->option . '">%s</label>',
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
      '<input id="' . $this->option . '" class="regular-text" name="' . $this->option . '" type="text" value="%s" />',
      ( Template::getOption( $this->id ) ? Template::getOption( $this->id ) : '' ) );
  

    if ( ! empty( $this->description ) ) {
      
      echo '<p>' . __( $this->description ) . $options . '</p>';
    }
  }
}