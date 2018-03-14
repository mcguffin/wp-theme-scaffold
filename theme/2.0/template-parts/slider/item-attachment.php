<?php

global $wp_query;

$post_class = array( 'item' );
if ( $wp_query->current_post == 0 ) {
	$post_class[] = 'active';
}
if ( $item_class = get_slider_arg( 'item_class' ) ) {
	$post_class = array_merge( $post_class, $item_class );
}

?>
<<?php the_slider_arg( 'item_tag' ); ?> <?php post_class( $post_class ); ?>>
	<?php echo wp_get_attachment_image( get_the_id(), get_slider_arg( 'size' ), false, get_slider_arg( 'image_attr' ) ); ?>
	<?php if ( get_slider_arg('caption') && $caption = get_the_excerpt() ) { ?>
		<figcaption><?php echo $caption; ?></figcaption>
	<?php } ?>
</<?php the_slider_arg( 'item_tag' ); ?>>