<div class="wrap">
  <h2>Comparison sets by page</h2>
  <p>Fine tune your sets here.</p>
  <?php foreach ( $compsets->getCompsets() as $pageName => $compset ) : ?>

    <details class="accordion">
      <summary><?php echo $pageName ?></summary>
      
      <?php foreach ( $compset as $set ) { ?>
        <?php $compsets->updateCompset( $set ); ?>
        <?php var_dump( '<pre>', $compsets->getOriginalSource( $set ), '</pre>'); ?>
      <?php } ?>
    </details>
  <?php endforeach; ?>
    
</div>