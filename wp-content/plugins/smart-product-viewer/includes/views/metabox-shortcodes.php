<div class="threesixty-shortcodes">
	<div class="dashicons dashicons-info"></div> You can also use shortcode generator when edit your post or page. 
	Click on "360&deg;" icon in text editor to see pop-up with all available parameters.
	<br/>
<?php if ( get_post_status( $post->ID ) == "auto-draft" ) : ?>
	<p class="description">You will see shortcode examples after save this Smart Product.</p>
<?php elseif ( $images == "") : ?>
	<p class="description">Please choose images and save product before using shortcodes.</p>
<?php else : ?>
	<p>Below are examples of some shortcode options (please use shortcode generator to see all options available). You can use these shortcodes as is or combine the options the way you want.</p>
	<table>
		<tr>
			<td><h4>Default</h4></td>
			<td><code>[smart-product id=<?php echo $post->ID; ?>]</code></td>
		</tr>
		<tr>
			<td>
				<h4>Add Scrollbar</h4>
			</td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> scrollbar=top]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> scrollbar=bottom]</code>
			</td>
			<td>
				<p class="description">More suitable for product animation than for 360 degree spin.</p>
			</td>
		</tr>
		<tr>
			<td><h4>Hide Border</h4></td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> border=false]</code>
			</td>
			<td>
				<p class="description">Use for transparent images.</p>
			</td>
		</tr>
		<tr>
			<td><h4>Hide Navigation</h4></td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> nav=false]</code>
			</td>
			<td>
				<p class="description">Use for small images, drag intreraction will be still active.</p>
			</td>
		</tr>
		<tr>
			<td><h4>Change Width</h4></td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> width=300]</code><br/>
			</td>
			<td>
				<p class="description">If not set width of first image will be used. Height will be based on image ratio.</p>
			</td>
		</tr>
		<tr>
			<td><h4>Choose Color</h4></td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> color=dark-blue]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=light-blue]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=red]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=brown]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=purple]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=gray]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=yellow]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> color=green]</code><br/>
			</td>
			<td>
				<p class="description"></p>
			</td>
		</tr>
		<tr>
			<td><h4>Choose Style</h4></td>
			<td>
				<code>[smart-product id=<?php echo $post->ID; ?> style=glow]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=fancy]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=wave]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=flat-round]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=flat-square]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=vintage]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=arrows]</code><br/>
				<code>[smart-product id=<?php echo $post->ID; ?> style=leather]</code><br/>
			</td>
			<td>
				<p class="description"></p>
			</td>
		</tr>
	</table>
<?php endif; ?>
</div>