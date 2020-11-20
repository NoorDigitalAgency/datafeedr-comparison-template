<?php 
/**
 * Custom Datafeedr template
 */

 use Noor\DatafeedrExt\Template;

global $compset;

if ( $compset->meets_min_num_product_requirement() || dfrcs_can_manage_compset() ) :

	if ( 1 === Template::getOption( 'show_title' ) ) {
		?>
			<h2><?php echo dfrcs_title(); ?></h2>
		<?php
	} ?>

	<ul class="dfrcs_compset">
		<?php if ( $dfrcs_products = dfrcs_products() ) {
			
			global $dfrcs_product;
			
			foreach ( $dfrcs_products as $dfrcs_product ) {
				?>
					<li class="<?php echo dfrcs_row_class(); ?>">
						<a target="_blank" href="<?php echo dfrcs_url(); ?>" rel="nofollow">
							<div class="item">
								<?php if ( 1 === Template::getOption( 'show_image' ) ) {
									?>
										<div class="dfrcs_image"><?php echo dfrcs_image(); ?></div>
									<?php
								}
								
								if ( 1 === Template::getOption( 'show_merchant' ) ) {
									?>
										<div class="dfrcs_logo"><?php echo dfrcs_logo(); ?></div>
									<?php
								}

								if ( 1 === Template::getOption( 'show_price' ) ) {
									?>
										<div class="dfrcs_price"><?php echo dfrcs_price(); ?></div>
									<?php
								} ?>
								<div class="dfrcs_link">
									<span class="dfrcs_action"><?php echo dfrcs_link_text(); ?></span>
								</div>
							</div>
							<?php echo dfrcs_promo(); ?>
						</a>
						<?php echo dfrcs_product_actions(); ?>
						<?php echo dfrcs_product_debug(); ?>
					</li>
				<?php 
			}
		} ?>
	</ul>

<?php else :

	$no_results_message = dfrcs_no_results_message();
	
	if ( ! empty( $no_results_message ) ) : ?>
		<div class="dfrcs_no_results_message"><?php echo $no_results_message; ?></div>
	<?php endif;
endif; ?>