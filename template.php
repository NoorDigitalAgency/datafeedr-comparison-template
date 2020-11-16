<?php global $compset; ?>

<?php $tmpl_options = get_option('dftemplate_settings') ?>

<?php if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) : ?>

	<h2><?php echo dfrcs_title(); ?></h2>

	<ul class="dfrcs_compset">
		<?php if ( $dfrcs_products = dfrcs_products() ) : global $dfrcs_product;

			if ( $tmpl_options['from_highest'] === '1' ) {

				$dfrcs_products = array_reverse( $dfrcs_products, true );
			}
			
			foreach ( $dfrcs_products as $dfrcs_product ) : ?>
				<li class="<?php echo dfrcs_row_class(); ?>">
					<a target="_blank" href="<?php echo dfrcs_url(); ?>" rel="nofollow">
						<div class="item">
              <?php if ( $tmpl_options['show_prod_img'] === '1') { ?>

							  <div class="dfrcs_image"><?php echo dfrcs_image(); ?></div>
              <?php } ?>
							<div class="dfrcs_logo"><?php echo dfrcs_logo(); ?></div>
              <?php if ( $tmpl_options['show_price'] === '1' ) { ?>

                <div class="dfrcs_price"><?php echo dfrcs_price(); ?></div>
              <?php } ?>
							<div class="dfrcs_link">
								<span class="dfrcs_action"><?php echo dfrcs_link_text(); ?></span>
							</div>
						</div>
						<?php echo dfrcs_promo(); ?>
					</a>
					<?php echo dfrcs_product_actions(); ?>
					<?php echo dfrcs_product_debug(); ?>
				</li>
			<?php endforeach; endif; ?>
	</ul>

<?php else : ?>

	<?php $no_results_message = dfrcs_no_results_message(); ?>
	<?php if ( ! empty( $no_results_message ) ) : ?>
		<div class="dfrcs_no_results_message"><?php echo $no_results_message; ?></div>
	<?php endif; ?>

<?php endif; ?>