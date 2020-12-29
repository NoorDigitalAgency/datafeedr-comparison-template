<?php

use Noor\DatafeedrExt\DfrExtention;

global $compset;

if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) {

  if ( $dfrcs_products = dfrcs_products() ) {
    
    global $dfrcs_product;

    DfrExtention::getArgs( $args = $compset->source->original );
    
    foreach( $dfrcs_products as $dfrcs_product ) {
      ?>
        <a target="_blank" href="<?php echo dfrcs_url(); ?>" rel="nofollow">
          <button class="<?php echo DfrExtention::getClassName( $args ); ?>" style="<?php echo DfrExtention::getStyles( $args ); ?>">
            <?php echo isset( $args['display_text'] ) ? $args['display_text'] : $dfrcs_product['name']; ?>
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