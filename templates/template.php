<?php 
/**
 * Custom Datafeedr template
 */

global $compset;

var_dump('<pre>', $compset, '</pre>');

$tmpl_options = get_option('dftemplate_settings');

if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) : ?>

	<h2><?php echo dfrcs_title(); ?></h2>

	<ul class="dfrcs_compset">
		<?php if ( $dfrcs_products = dfrcs_products() ) : global $dfrcs_product;

			foreach ( $dfrcs_products as $dfrcs_product ) : ?>
				<li class="<?php echo dfrcs_row_class(); ?>">
					<a target="_blank" href="<?php echo dfrcs_url(); ?>" rel="nofollow">
						<div class="item">
							<?php
								if ( 1 == $tmpl_options['show_prod_img'] ) {

									echo '<div class="dfrcs_image">' . dfrcs_image() . '</div>';
								}

								if ( 1 == $tmpl_options['show_merchant'] ) {

									echo '<div class="dfrcs_logo">' . dfrcs_logo() . '</div>';
								}

								if ( 1 == $tmpl_options['show_price'] ) {

									echo '<div class="dfrcs_price">' . dfrcs_price() .'</div>';
								}
							?>
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

<?php else :

	$no_results_message = dfrcs_no_results_message();
	
	if ( ! empty( $no_results_message ) ) : ?>
		<div class="dfrcs_no_results_message"><?php echo $no_results_message; ?></div>
	<?php endif;
endif; ?>