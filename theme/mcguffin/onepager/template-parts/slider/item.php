<?php

global $wp_query;
$is_first = $wp_query->current_post == 0;

?>
<<?php the_slider_arg( 'item_tag' ); ?> <?php post_class( get_slider_arg( 'item_class' ) . ( $is_first ? ' active' : '' ), get_the_ID() ); ?>>
	<h3 class="slider-item-title"><?php the_title(); ?></h3>
	<div class="slider-item-content"><?php the_content(); ?></div>
</<?php the_slider_arg( 'item_tag' ); ?>>