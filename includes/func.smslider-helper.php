<?php
function smsd_get_no_image(  $size, $alt ){
	return SMSlider::get_no_image( $size, $alt );
}

function smsd_get_thumbnail( $size='thumbnail' ){
	return SMSlider::get_thumbnail( $size );
}

function smsd_get_post_meta( $post_id, $key ){
	return SMSlider::get_post_meta( $post_id, $key );
}

function smsd_get_slider( $id ){
	return do_shortcode("[smslider {$id}]");
}

//@since 1.0.3 - update for boostrap
add_action( 'smslider/beforejs/carousel', 'smsd_fix_js_bootstrap',10, 1 );
function smsd_fix_js_bootstrap( $id ){
	echo "$('#{$id}').find('.item').first().addClass('active');";
}

add_filter( 'smslider/afterloop/bootstrap', 'smsd_add_control_bootstrap', 10, 3 );
function smsd_add_control_bootstrap( $control, $id, $no ){
	ob_start();
	?>
	<!-- Indicators -->
	<ol class="carousel-indicators">
		<?php for( $i = 0; $i < $no - 1; $i++ ){
			$active = $i === 0 ? 'class="active"' : '';
		?>
	    <li data-target="#<?php echo $id;?>" data-slide-to="<?php echo $i;?>" <?php echo $active;?>></li>
	    <?php }?>
	</ol>
	<!-- Left and right controls -->
	<a class="left carousel-control" href="#<?php echo $id;?>" role="button" data-slide="prev">
	   <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	   <span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#<?php echo $id;?>" role="button" data-slide="next">
	    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
	    <span class="sr-only">Next</span>
	</a>
	<?php
	$control = ob_get_contents();
	ob_clean();
	return $control;
}

?>