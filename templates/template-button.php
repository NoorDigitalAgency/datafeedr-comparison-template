<?php

global $compset;

if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) {

  if ( $dfrcs_products = dfrcs_products() ) {

    global $dfrcs_product;

    $count = 0;

    $args = $compset->source->original;

    $display_num = ( isset( $args['display_num'] ) && !empty( $args['display_num'] ) )
      ? $args['display_num']
      : count( $dfrcs_products );
    
    $button_text = ( isset( $args['display_text'] ) && !empty( $args['display_text'] ) )
      ? $args['display_text']
      : '';
    
    foreach( $dfrcs_products as $dfrcs_product ) {

      $count++;

      if ( $count > $display_num ) {
        break;
      }

      ?>
        <a target="_blank" href="<?php echo dfrcs_url(); ?>" rel="nofollow">
          <button>
            <?php echo empty( $button_text ) ? $dfrcs_product['name'] : $button_text; ?>
          </button>
        </a>
        <?php echo dfrcs_product_actions(); ?>
				<?php echo dfrcs_product_debug(); ?>
      <?php
    }
  }
} else { 

	$no_results_message = dfrcs_no_results_message();
	
  if ( ! empty( $no_results_message ) ) {
		?>
      <div class="dfrcs_no_results_message"><?php echo $no_results_message; ?></div>
    <?php 
  }
} ?>