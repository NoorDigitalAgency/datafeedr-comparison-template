<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\{DfrExtention, OptionsField, AdminAlert};

class Options extends DfrExtention {
  
  private $fields;

  public function __construct () {
    
    $this->dependencies();
  }

  protected function dependencies () {

    $this->fields = json_decode( file_get_contents( __DIR__ . '/../admin-fields.json' ), true );

    $this->setDisplayMethod();

    new AdminAlert();

    add_action( 'admin_menu', [$this, 'addAdminPage'], 999 );
    add_action( 'admin_init', [$this, 'createAdminOptions'] );
  }

  /**
   * templateSubMenuPage
   * 
   * Registers sub menu page to Datafeedr API menu page
   * 
   * @return void
   */
  public function addAdminPage (): void {

    add_submenu_page( 
      'dfrapi', 
      __( 'Template Options' ),
      __( 'Template Options' ),
      'manage_options',
      'tmpl_options',
      [$this, 'renderAdminPage'],
      NULL
    );
  }

  /**
   * 
   * templateFields
   * 
   * Registers page options group for storing settings
   * 
   * @return void
   */
  public function createAdminOptions (): void {
    
    foreach ( $this->getActiveNetworks() as $network ) {
      
      // Add field input on each active network
      $this->fields['tmpl_options_uri']['fields'][] = [
        'id'          => 'uri_ext_' . $network['_id'],
        'title'       => ( 'AffiliateWindow' == $network['group'] ) ? 'Awin' : $network['group'],
        'callback'    => 'createTextField',
        'label'       => '',
        'description' => 'Available options: ',
        'options'     => ['{page}', '{product}', '{price}', '{finalprice}']
      ];
    }

    register_setting( 'tmpl_options', 'tmpl_options' );

    foreach ( $this->fields as $section => $set ) {

      add_settings_section( 
        $section, 
        __( $set['title'] ), 
        [$this, $set['callback']], 
        'tmpl_options' 
      );

      foreach ( $set['fields'] as $field ) {
        
        add_settings_field( 
          $field['id'], 
          __( $field['title'] ), 
          [new OptionsField( 'tmpl_options', $field ), $field['callback']], 
          'tmpl_options', 
          $section 
        );
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
  public function renderAdminPage () {
    
    echo '<div class="wrap" id="tmpl_options">';

    if ( is_array( $log = $this->getWarnings() ) ) {

      foreach ( $log as $warning ) {

        printf(
          '<div style="display: block;" class="update-nag notice notice-warning">Page: <strong>%s</strong> is displaying %s sets with no products. <a href="%s" target="_blank">inspect</a></div>',
          get_the_title( $warning['page_id'] ),
          $warning['warnings'],
          $warning['permalink']
          // $warning['editlink']
        );
      }
    }

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
      __( 'These settings control appending query parameter to product url.' ) );
  }
}