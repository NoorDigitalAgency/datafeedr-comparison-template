<div class="wrap">
  <h2><?php _e('Datafeedr Template Options'); ?></h2>

  <p><?php _e( 'These template options are global and apply to all Comparison Set tables.'); ?></p>
  <form method="post" action="options.php">

  <?php settings_fields( 'dftemplate_settings_group' ); ?>

    <table class="form-table">
      <h3>Display options</h3>
      <tbody>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Dsiplay table title' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[show_title]" name="dftemplate_settings[show_title]" type="checkbox" value="1" <?php echo checked( 1, $this->options['show_title'], false ); ?> />
            <label class="description" for="dftemplate_settings[show_title]"><?php _e('Check this to display table title.'); ?></label>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Dsiplay product image' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[show_prod_img]" name="dftemplate_settings[show_prod_img]" type="checkbox" value="1" <?php echo checked( 1, $this->options['show_prod_img'], false ); ?> />
            <label class="description" for="dftemplate_settings[show_prod_img]"><?php _e('Check this to display product image in table.'); ?></label>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Dsiplay merchant brand/logo' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[show_merchant]" name="dftemplate_settings[show_merchant]" type="checkbox" value="1" <?php echo checked( 1, $this->options['show_merchant'], false ); ?> />
            <label class="description" for="dftemplate_settings[show_merchant]"><?php _e('Check this to display merchant brand/logo.'); ?></label>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Dsiplay price' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[show_price]" name="dftemplate_settings[show_price]" type="checkbox" value="1" <?php echo checked( 1, $this->options['show_price'], false ); ?> />
            <label class="description" for="dftemplate_settings[show_price]"><?php _e('Check this to display price in table.'); ?></label>
          </td>
        </tr>
      </tbody>
    </table>
    <table class="form-table">
      <h3>Sorting options</h3>
      <tbody>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Display ASC/DESC' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[sort_desc]" name="dftemplate_settings[sort_desc]" type="checkbox" value="1" <?php echo checked( 1, $this->options['sort_desc'], false ); ?> />
            <label class="description" for="dftemplate_settings[sort_desc]"><?php _e('Check this to display products in DESC order'); ?></label>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row" valign="top">
            <?php _e( 'Sortby:' ); ?>
          </th>
          <td>
            <input id="dftemplate_settings[order_by]" class="regular-text" name="dftemplate_settings[order_by]" type="text" value="<?php echo ( isset( $this->options['order_by'] ) ? $this->options['order_by'] : '' ); ?>" />
            <p>Available options: <code>merchant</code> <code>finalprice</code></p>
          </td>
        </tr>
      </tbody>
    </table>
    <table class="form-table">
      <h3>URI options</h3>
      <tbody>
        <?php if ( is_array( $this->active_networks ) && ! empty( $this->active_networks ) ) {
        
          foreach ( $this->active_networks as $network ) {

            $html = '<tr valign="top">';

            $html .= sprintf( '<th scope="row" valign="top">%s</th>', 
              __( ( 'AffiliateWindow' == $network['group'] ) ? 'Awin' : $network['group'] )
            );

            $html .= '<td>';

            $html .= sprintf( '<input id="%1$s" class="%2$s" name="%1$s" type="text" placeholder="%3$s" value="%4$s" />',
              'dftemplate_settings[uri_ext_' . $network['_id'] . ']',
              'regular-text',
              'epi={page}-{product}',
              isset( $this->options['uri_ext_' . $network['_id']] )
                ? $this->options['uri_ext_' . $network['_id']]
                : ''
            );

            $html .= '<p>Available placeholders: <code>{page}</code> <code>{product}</code> <code>{price}</code> <code>{finalprice}</code></p>';

            $html .= '</td></tr>';

            echo $html;
          }
        } ?>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>
</div>