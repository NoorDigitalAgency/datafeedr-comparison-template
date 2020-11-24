<?php

namespace Noor\DatafeedrExt;

use Noor\DatafeedrExt\{DfrExtention, OptionsField};

class Options extends DfrExtention {
  
  private $fields;

  public function __construct () {
    
    $this->dependencies();
  }

  protected function dependencies () {

    $this->fields = json_decode( file_get_contents( __DIR__ . '/../admin-fields.json' ), true );

    $this->setDisplayMethod();

    add_action( 'admin_menu', [$this, 'addAdminPage'], 999 );
    add_action( 'admin_init', [$this, 'createAdminOptions'] );
    add_action( 'admin_init', [$this, 'checkLog'], 99 );
    add_filter( 'dfrcs_no_results_message', [$this, 'noResults'], 10, 2 );
    add_filter( 'add_menu_classes', [$this, 'alertEmptysets'], 999 );
  }

  /**
   * noResults
   * 
   * @param string $message
   * 
   * @param Dfrcs $compset
   */
  public function noResults ( string $message, \Dfrcs $compset ) {

    $page = get_the_title( $compset->source->original['post_id'] );
        
    $options = get_option( 'tmpl_options' );
    
    if ( ! in_array( md5( $compset->date_created ), $options['log'][$page] ) ) {

      $options['log'][$page][md5($compset->date_created)] = $compset->source->original['post_id'];
  
      update_option( 'tmpl_options', $options );
    }

    return $message;
  }

  public function checkLog () {

    if ( isset( $_GET['dfrtmpl_log'] ) ) {

      $options = get_option( 'tmpl_options' );

      if ( $options['log'][$_GET['post']][$_GET['dfrtmpl_log']] ) {

        unset( $options['log'][$_GET['post']][$_GET['dfrtmpl_log']] );

        update_option( 'tmpl_options', $options );
      }
    }
  }

  public function alertEmptysets ( $menu ) {

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

    $log = $this->getOption( 'log' );

    if ( is_array( $log = $this->getWarnings() ) ) {
      
      $count = count( $log );

      $key = array_keys( $dfrMenu );

      $index = end( $key );
      
      $menu[$index][0] .= ' <span class="update-plugins count-' . $count . '"><span class="plugin-count">' . (string) $count . '</span></span>';
    }
    
    return $menu;
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

    if ( is_array( $warnings = $this->getWarnings() ) ) {

      foreach ( $warnings as $warning ) {

        printf(
          '<div style="display: block;" class="update-nag notice notice-warning">%s <a href="%s">moderate</a></div>',
          'Page: <strong>' . $warning['page'] . '</strong> is displaying ' . $warning['sets'] . ' sets with no products.',
          $warning['permalink']
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
      __( 'These settings control appending query parameter to product url  .' ) );
  }
}