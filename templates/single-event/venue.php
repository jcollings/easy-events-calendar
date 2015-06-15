<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/06/2015
 * Time: 19:44
 */ ?>
<div class="jce-two-cols">
    <div class="jce-event-venue jce-one-col">
        <h2>Venue</h2>
        <p><span class="jce-meta-title">Name:</span> <?php jce_event_venue_meta(); ?><br />
            <span class="jce-meta-title">Address:</span> <?php jce_event_venue_meta('address'); ?><br />
            <span class="jce-meta-title">City:</span> <?php jce_event_venue_meta('city'); ?><br />
            <span class="jce-meta-title">Postcode:</span> <?php jce_event_venue_meta('postcode'); ?></p>
    </div>