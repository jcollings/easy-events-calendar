<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 15/06/2015
 * Time: 20:43
 */
$start_date = jce_event_start_date('jS', false);
$end_date = jce_event_end_date('jS', false);
?>
<div class="jce-event-meta">
    <?php
    if($start_date != $end_date){
        echo "<span><i class='fa fa-calendar-o'></i> ".jce_event_start_date('jS F Y g:i a', false)." - ".jce_event_end_date('jS F Y g:i a', false)."</span>";
    }else{
        echo "<span><i class='fa fa-calendar-o'></i> ".jce_event_start_date('jS F Y g:i a', false)." - ".jce_event_end_date('g:i a', false)."</span>";
    }
    ?>
</div>