<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

<?php the_excerpt(); ?>

<?php var_dump(JCE()->event->get_post_meta()); ?>