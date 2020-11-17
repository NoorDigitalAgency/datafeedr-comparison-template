<div class="wrap">
  <h2><?php _e('Datafeedr Template Options'); ?></h2>

  <p><?php _e( 'These template options are global and apply to all Comparison Set tables.'); ?></p>
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
            <?php _e( 'Dsiplay merchants brand/logo' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[show_merchant]" name="dftemplate_settings[show_merchant]" type="checkbox" value="1" <?php echo checked( 1, $options['show_merchant'], false ); ?> />
            <label class="description" for="dftemplate_settings[show_merchant]"><?php _e('Check this to display merchants brand/logo.'); ?></label>
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
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Dsiplay price from Highest to Lowest' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[from_highest]" name="dftemplate_settings[from_highest]" type="checkbox" value="1" <?php echo checked( 1, $options['from_highest'], false ); ?> />
            <label class="description" for="dftemplate_settings[from_highest]"><?php _e('Check this to display products from highest price to lowest.'); ?></label>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>
</div>