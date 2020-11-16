<?php
/**
 * Plugin Name: Datafeedr Comparison Template
 * Description: Template extention options to datafeedr Comparison Sets
 * Version: 0.0.1
 * Author: Noor Digital Agency
 * Author URI: https://noordigital.com 
 */

// Require composer autoloader
if ( ! file_exists( $autoload = plugin_dir_path( __FILE__ ) .'vendor/autoload.php' ) ) {

  wp_die( __( 'This plugin requires Composer.' ) );
}

require $autoload;

$package = json_decode( file_get_contents( __DIR__ . '/composer.json' ), false );

/**
 * Plugin updater to push updates from github to wp admin interface
 */
$plugin_updater = Puc_v4_Factory::buildUpdateChecker(
	$package->homepage,
	__FILE__,
	$package->name
);

// Stable branch master
// $plugin_updater->setBranch( 'master' );

$plugin_updater->getVcsApi()->enableReleaseAssets();

if ( ! class_exists( 'Dfrcs' ) ) {

  wp_die( 'This plugin relies on datafeedr Comparison Sets plugin.' );
}

add_action( 'admin_menu', function() {

  add_options_page(
    __( 'Datafeedr Template Options' ),
    __( 'Datafeedr Template Options' ),
    'manage_options',
    'dftemplate-settings',
    'noor_options_html'
  );
});

add_action( 'admin_init', function () {

  register_setting( 
    'dftemplate_settings_group', 
    'dftemplate_settings'
  );
});

if ( !function_exists('noor_options_hmtl') ) {

  function noor_options_html() {

    $options = get_option('dftemplate_settings');
    ?>
      <div class="wrap">
        <h2><?php _e('Datafeedr Template Options'); ?></h2>

        <form method="post" action="options.php">

        <?php settings_fields( 'dftemplate_settings_group' ); ?>

        <table class="form-table">
          <tbody>
          <tr valign="top">
              <th scope="row" valign="top">
                <?php _e( 'Dsiplay product image' ); ?>
              </th>
              <td>
                <input id="dftemplate_settings[show_prod_img]" name="dftemplate_settings[show_prod_img]" type="checkbox" value="1" <?php echo checked( 1, $options['show_prod_img'], false ); ?> />
                <label class="description" for="dftemplate_settings[show_prod_img]"><?php _e('Check this to display product image in table.'); ?></label>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row" valign="top">
                <?php _e( 'Dsiplay price' ); ?>
              </th>
              <td>
                <input id="dftemplate_settings[show_price]" name="dftemplate_settings[show_price]" type="checkbox" value="1" <?php echo checked( 1, $options['show_price'], false ); ?> />
                <label class="description" for="dftemplate_settings[show_price]"><?php _e('Check this to display price in table.'); ?></label>
              </td>
            </tr>
          </tbody>
        </table>

        <?php submit_button(); ?>
        </form>
      </div>
    <?php
  }
}

add_filter( 'dfrcs_template', function ( $template, $instance ) {

  // var_dump('<pre>', $template, '</pre>');

  return plugin_dir_path( __FILE__ ) . '/template.php';
}, 99, 2);