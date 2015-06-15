<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/06/2015
 * Time: 20:47
 */

global $post;
$tags = wp_get_object_terms( $post->ID, 'event_tag', array('fields' => 'all') );
$tag_output = '';
if($tags){
    foreach($tags as $tag){
        if($tag_output != ''){
            $tag_output .= ', ';
        }
        $tag_output .= '<a href="' . get_term_link( $tag, 'event_tag' ) . '">' . $tag->name . '</a>';
    }
}

$categories = wp_get_object_terms( $post->ID, 'event_category', array('fields' => 'all') );
$cat_output = '';
if($categories){
    foreach($categories as $cat){
        if($cat_output != ''){
            $cat_output .= ', ';
        }
        $cat_output .= '<a href="' . get_term_link( $cat, 'event_category' ) . '">' . $cat->name . '</a>';
    }
}
?>
<?php
// output event tags
if( !empty( $tag_output ) ): ?>
    <p><span class="jce-meta-title"><i class="fa fa-bookmark"></i> Tagged:</span> <?php echo $tag_output; ?></p>
<?php endif; ?>

<?php
// output event categories
if( !empty( $cat_output ) ): ?>
    <p><span class="jce-meta-title"><i class="fa fa-tag"></i> Categories:</span> <?php echo $cat_output; ?></p>
<?php endif;