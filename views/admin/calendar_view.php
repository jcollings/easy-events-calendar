<?php
/**
 * Add new Calendar
 */
if(isset($_POST['action'])){
    switch($_POST['action']){
        case 'manage_calendars':
            $cal_name = isset($_POST['cal_name']) && !empty($_POST['cal_name']) ? $_POST['cal_name'] : $_POST['cal_name'];
            wp_insert_term( $cal_name, 'event_cals');
        break;
    }
    
}
?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-events"><br></div>
	<h2>Events Calendars</h2>

    <div id="poststuff" class="simple_events">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <?php
                $cal = new jc_events_calendar();
                
                $year = isset($_GET['xyear']) ? $_GET['xyear'] : false;
                $month = isset($_GET['xmonth']) ? $_GET['xmonth'] : false;

                $cal->prev_year_link = false;
                $cal->next_year_link = false;
                $cal->set_week_start('Mon');
                $cal->set_month($year,$month);

                echo $cal->render();
                ?>
            </div>

            <div id="postbox-container-1" class="postbox-container" style="margin-top:42px;">
                <div id="postimagediv" class="postbox ">
                    <h3 class="hndle"><span>Calendars</span> - <a href="edit-tags.php?taxonomy=event_cals">Manage</a></h3>
                    <div class="inside">
                        <form method="post">
                            <input type="hidden" name="action" value="manage_calendars">
                            <div id="category-all">
                            <?php 
                            $calendars = get_terms( 'event_cals', array('hide_empty' => false));
                            foreach($calendars as $calendar){
                                ?>
                                <div class="input checkbox">
                                    <input type="checkbox" name="<?php echo $calendar->slug; ?>" value="1" checked="checked" />
                                    <label><?php echo $calendar->name; ?></label>
                                </div>
                                <?php
                            }
                            ?>
                            </div>
                            <label>Add Calendar: </label>
                            <input type="text" name="cal_name" />
                        </form>
                    </div>
                </div><!-- /#postimagediv -->
<?php /*
                <div id="postimagediv" class="postbox ">
                    <h3 class="hndle"><span>Manage Calendars</span></h3>
                    <div class="inside">
                        <form method="post">
                            <input type="hidden" name="action" value="calendar_settings">
                            <select id="calendar_list">
                                <option></option>
                            <?php 
                            $calendars = get_terms( 'event_cals', array('hide_empty' => false));
                            foreach($calendars as $calendar){
                                ?>
                                <option value="<?php echo $calendar->slug; ?>"><?php echo $calendar->name; ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </form>
                    </div>
                </div><!-- /#postimagediv -->
*/ ?>
            </div>
        </div>
    </div>

</div>