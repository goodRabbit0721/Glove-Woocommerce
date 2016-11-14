<input type="button" id="smart-product-upload-photos" value="Choose Images"/>
<?php if ( $images != array() ) : ?>
<input type="button" id="smart-product-reorder" value="Reverse Images Order"/>
<p class="description">You can drag below images to change the order.</p>
<?php endif; ?>
<div id="smart-product-images-wrap">
	<ul id="smart-product-sortable">
	<?php foreach ( $images as $id ) : ?>
		<li data-id="<?php echo $id; ?>"><?php echo wp_get_attachment_image( $id, 'smart-product-thumb' ); ?></li>	
	<?php endforeach; ?>
	</ul>
</div>
<div class="clear"></div>
