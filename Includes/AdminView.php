<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\Template;

class AdminView {

  private $fields;

  private $activeNetworks;

  private $networksCount = 0;

  public function __construct ( $activeNetworks, $adminFields ) {

    $this->activeNetworks = $activeNetworks;

    $this->fields = $adminFields;

    // Make sure to set display type to php in order to have post params available
    $dfrcs_options = get_option('dfrcs_options');
    if ( $dfrcs_options && $dfrcs_options['display_method'] != 'php' ) {

      $dfrcs_options['display_method'] = 'php';

      update_option( 'dfrcs_options', $dfrcs_options ); 
    }
  }

    /**
   * templateSubMenuPage
   * 
   * Registers sub menu page to Datafeedr API menu page
   * 
   * @return void
   */
  public function templateSubMenuPage (): void {

    add_submenu_page( 
      'dfrapi', 
      __( 'Template Options' ),
      __( 'Template Options' ),
      'manage_options',
      'tmpl_options',
      [$this, 'optionsOutput'],
      NULL
    );
  }

  /**
   * templateFields
   * 
   * Registers page options group for storing settings
   * 
   * @return void
   */
  public function templateFields (): void {
    
    foreach ( $this->activeNetworks as $network ) {
      
      $this->fields['tmpl_options_uri']['fields'][] = [
        'id'       => 'uri_ext_' . $network['_id'],
        'title'    => ( 'AffiliateWindow' == $network['group'] ) ? 'Awin' : $network['group'],
        'callback' => 'uriExtOutput'
      ];
    }

    register_setting( 'tmpl_options', 'tmpl_options' );

    foreach ( $this->fields as $section => $set ) {

      add_settings_section( $section, __( $set['title'] ), [$this, $set['callback']], 'tmpl_options' );

      foreach ( $set['fields'] as $field ) {
        
        add_settings_field( $field['id'], __( $field['title'] ), [$this, $field['callback']], 'tmpl_options', $section );
      }
    }
  }

  /**
   * optionsOutput
   * 
   * Callback for rendering the page markup
   * 
   * @return string
   */
  public function optionsOutput () {
    
    echo '<div class="wrap" id="tmpl_options">';

    printf( '<h2>%s</h2><p>%s<a href="%s" target="_blank">%s</a></p>',
      __( 'Datafeedr Comparison Template' ),
      __( 'All settings apply globaly to comparison template. For single template options refere to extra shortcode args: ' ),
      'https://github.com/NoorDigitalAgency/datafeedr-comparison-template',
      __( 'HERE' ) );

    echo '<form method="post" action="options.php">';
    
    echo wp_nonce_field( 'tmpl-update_options' );
    echo settings_fields( 'tmpl_options' );
    echo do_settings_sections( 'tmpl_options' );
    echo submit_button();

    echo '</form></div>';
  }

  /**
   * displaySectionOutput
   * 
   * @return string
   */
  public function displaySectionOutput () {
    
    printf( '<p>%s</p>', 
      __( 'These settings control how the template appears on your website.' ) );
  }

  /**
   * sortingSectionOutput
   * 
   * @return string
   */
  public function sortingSectionOutput () {

    printf( '<p>%s</p>', 
      __( 'These settings control how the template sorts the products.' ) );
  }

  /**
   * uriSectionOutput
   * 
   * @return string
   */
  public function uriSectionOutput () {

    printf( '<p>%s</p>', 
      __( 'These settings control appending query parameter to product url  .' ) );
  }

  /**
   * showTitleOutput
   * 
   * @return string
   */
  public function showTitleOutput () {
    
    printf( 
      '<input id="tmpl_options[show_title]" name="tmpl_options[show_title]" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( 'show_title' ), false ) );

    printf( 
      '<label class="description" for="tmpl_options[show_title]">%s</label>',
      __( 'Check this to display table title.' ) );
  }

  /**
   * showImageOutput
   * 
   * @return string
   */
  public function showImageOutput () {
    
    printf( 
      '<input id="tmpl_options[show_image]" name="tmpl_options[show_image]" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( 'show_image' ), false ) );

    printf( 
      '<label class="description" for="tmpl_options[show_image]">%s</label>',
      __( 'Check this to display product image.' ) );
  }

  /**
   * showMerchantOutput
   * 
   * @return string
   */
  public function showMerchantOutput () {
    
    printf( 
      '<input id="tmpl_options[show_merchant]" name="tmpl_options[show_merchant]" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( 'show_merchant' ), false ) );

    printf( 
      '<label class="description" for="tmpl_options[show_merchant]">%s</label>',
      __( 'Check this to display merchant logo.' ) );
  }

  /**
   * showPriceOutput
   * 
   * @return string
   */
  public function showPriceOutput () {
    
    printf( 
      '<input id="tmpl_options[show_price]" name="tmpl_options[show_price]" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( 'show_price' ), false ) );

    printf( 
      '<label class="description" for="tmpl_options[show_price]">%s</label>',
      __( 'Check this to display product price.' ) );
  }

  /**
   * orderDescOutput
   * 
   * @return string
   */
  public function orderDescOutput () {
    
    printf(
      '<input id="tmpl_options[order_desc]" name="tmpl_options[order_desc]" type="checkbox" value="1" %s />',
      checked( 1, Template::getOption( 'sort_desc' ), false ) );

    printf(
      '<label class="description" for="tmpl_options[sort_desc]">%s</label>',
      __( 'Check this to display products in DESC order' ) );
  }

  /**
   * orderByOutput
   * 
   * @return string
   */
  public function orderByOutput () {
    
    printf(
      '<input id="tmpl_options[order_by]" class="regular-text" name="tmpl_options[order_by]" type="text" value="%s" />',
      ( Template::getOption( 'order_by' ) ? Template::getOption( 'order_by' ) : '' ) );
      
    printf(
      '<p>Available options: <code>%s</code> <code>%s</code></p>',
      'merchant',
      'finalprice' );
  }

  /**
   * uriExtOutput
   * 
   * @return string
   */
  public function uriExtOutput () {
    
    $this->networksCount++;

    $keys = array_keys( $this->activeNetworks );

    $network = $this->activeNetworks[$keys[$this->networksCount - 1]];

    printf( 
      '<input id="%1$s" class="regular-text" name="%1$s" type="text" placeholder="%2$s" value="%3$s" />',
      'tmpl_options[uri_ext_' . $network['_id'] . ']',
      'epi={page}-{product}',
      ( Template::getOption( 'uri_ext_' . $network['_id'] )
        ? Template::getOption( 'uri_ext_' . $network['_id'] )
        : '' ) );

    printf(
      '<p>Available placeholders: <code>{%s}</code> <code>{%s}</code> <code>{%s}</code> <code>{%s}</code></p>',
      'page',
      'product',
      'price',
      'finalprice' );
  }
}