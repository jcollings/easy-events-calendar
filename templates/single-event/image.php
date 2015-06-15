<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/06/2015
 * Time: 18:52
 */

global $post;
$attachment_id = get_post_thumbnail_id($post->ID);

if(!$attachment_id)
    return;

$image_size = apply_filters( 'jce/single_image_size', 'full');
$src = wp_get_attachment_image_src( $attachment_id, $image_size);
?>
<div class="jce-event-image">
    <img src="<?php echo $src[0]; ?>" title="<?php echo get_the_title($attachment_id ); ?>" alt="<?php echo get_the_title($attachment_id ); ?>" width="100%" />
</div>