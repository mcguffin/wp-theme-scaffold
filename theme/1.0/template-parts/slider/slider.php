<?php

global $wp_query;

$slider_id = '';

$slider_class = get_slider_arg('class');

if ( get_slider_arg('caption') ) {
	$slider_class .= ' caption';
} else {
	$slider_class .= ' no-caption';
}

?>
<div id="<?php the_slider_arg('id'); ?>" class="carousel slide <?php echo $slider_class ?>" <?php echo get_slider_arg('autoplay') ? 'data-ride="carousel"' : ''; ?> data-pause="hover" data-interval="<?php echo absint( get_slider_arg('interval') * 1000 ); ?>">
	<?php if ( $wp_query->post_count > 1 ) {?>
		<ol class="carousel-indicators">
			<?php
				for ( $i=0; $i < $wp_query->post_count; $i++ ) {
					?>
					<li data-target="#<?php the_slider_arg('id'); ?>" data-slide-to="<?php echo $i ?>" <?php echo $i===0 ? 'class="active"' : ''; ?>></li>
					<?php
				}
			?>
		</ol>
	<?php } ?>
	<div class="carousel-inner" role="listbox">
		<?php

			while ( have_posts() ) {

				the_post();

				get_template_part( 'template-parts/slider/item', get_post_type() );

			}

		?>
	</div>
</div>
