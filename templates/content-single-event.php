<h1><?php the_title(); ?></h1>

<?php the_content(); ?>
<hr />

Venue: <?php jce_event_venue_meta(); ?><br />
Address: <?php jce_event_venue_meta('address'); ?><br />
Address: <?php jce_event_venue_meta('city'); ?><br />
Postcode: <?php jce_event_venue_meta('postcode'); ?>
<hr />
Organiser: <?php jce_event_organiser_meta(); ?><br />
Phone: <?php jce_event_organiser_meta('phone'); ?><br />
Email: <?php jce_event_organiser_meta('email'); ?><br />
Website: <?php jce_event_organiser_meta('website'); ?>
<hr />

<?php var_dump(JCE()->event->get_post_meta()); ?>