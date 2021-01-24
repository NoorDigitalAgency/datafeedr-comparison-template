<div class="wrap" id="tmpl_options">

  <?php 
    if ( is_array( $warnings ) ) {

      foreach ( $warnings as $warning ) {
  
        printf(
          '<div style="display: block;" class="update-nag notice notice-warning">Page: <strong>%s</strong> is displaying %s sets with no products. <a href="%s" target="_blank">inspect</a></div>',
          get_the_title( $warning['page_id'] ),
          $warning['warnings'],
          $warning['permalink']
          // $warning['editlink']
        );
      }
    } 
  ?>

  <h2><?php _e( 'Datafeedr Comparison Template' ); ?></h2>
  <p><?php _e( 'All settings apply globaly to comparison template. For single template options refere to extra shortcode args: ' ); ?>
    <a href="https://github.com/NoorDigitalAgency/datafeedr-comparison-template" target="_blank"><?php _e( 'HERE' ); ?></a>
  </p>

  <form method="post" action="options.php">
    <?php
      echo wp_nonce_field( 'tmpl-update_options' );
      echo settings_fields( 'tmpl_options' );
      echo do_settings_sections( 'tmpl_options' );
      echo submit_button();
    ?>
  </form>
</div>