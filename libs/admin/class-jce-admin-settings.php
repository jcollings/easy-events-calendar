<?php

class JCE_Admin_Settings{

	public function __construct(){
		
		add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );
		add_action( 'admin_init', array($this, 'register_settings' ));
	}

	public function register_menu_pages(){     

        // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page('edit.php?post_type=event', 'Settings', 'Settings', 'manage_options', 'jce-settings', array($this, 'admin_settings_page'));
	}

	public function admin_settings_page(){
		global $tabs;
		global $wptickets;
		
		$tabs = array(
		    'base_settings' => array(
		        'title' => 'General Settings'
		    )
		);

		// hook to extends setting tabs
		do_action('jce/output_settings_tabs', $tabs);

		// include view file
        ?>
		<div class="wrap">
            <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
            <h2>Events Settings</h2>

            <div class="settings">

                <?php $current_tab = isset($_GET['tab']) ? $_GET['tab'] : key($tabs); ?>

                <h2 class="nav-tab-wrapper">
                    <?php foreach($tabs as $id => $tab): ?>
                    <a href="edit.php?post_type=event&page=jce-settings&tab=<?php echo $id; ?>" class="nav-tab <?php if($id == $current_tab): ?>nav-tab-active<?php endif; ?>"><?php echo $tab['title']; ?></a>
                    <?php endforeach; ?>
                </h2>

                <form action="options.php" method="post" enctype="multipart/form-data">  
                    <?php
                    settings_fields( $current_tab );
                    do_settings_sections($current_tab);
                    ?>  
                    <p class="submit">  
                        <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />  
                    </p>  
                </form> 
            </div>
        </div>
        <?php
	}

	public function register_settings(){

    	$this->load_settings_api();

    	foreach($this->settings_sections as $section => $options){

    		//register settings
    		foreach($options['fields'] as $field){
    			// register_setting($this->settings_optgroup, $field['setting_id'], array($this, 'save_setting'));
    			register_setting($options['section']['page'], $field['setting_id'], array($this, 'save_setting'));
    		}

    		// register section
    		add_settings_section($section, $options['section']['title'], array($this, 'section_callback'), $options['section']['page']);

    		//register fields
    		foreach($options['fields'] as $field){
    			$args = array(
		            'type' => $field['type'],
		            'field_id' => $field['id'],
		            'section_id' => $field['section'],
		            'setting_id' => $field['setting_id'],
		        );

		        if(isset($field['value'])){
		        	$args['value'] = $field['value'];
		        }

		        if(isset($field['multiple'])){
		        	$args['multiple'] = $field['multiple'];
		        }

		        if(isset($field['choices'])){
		        	$args['choices'] = $field['choices'];
		        }

    			add_settings_field($field['id'], $field['label'], array($this, 'field_callback'), $options['section']['page'], $field['section'], $args);
    		}
    	}
    }

    /**
     * Validate Save Settings
     * 
     * @param  array
     * @return array
     */
    public function save_setting($args){
        $args = apply_filters( 'jce/settings_save', $args );
    	return $args;
    }

    /**
     * Load Support System Settings
     * 
     * Setup settings to be outputted via wordpress Settings API
     * 
     * @return void
     */
    private function load_settings_api(){

    	// $terms = get_terms( 'department', array('hide_empty' => 0) );
    	// $support_groups = array('' => 'Select a Term');
    	
    	// foreach($terms as $term){
    	// 	$support_groups[$term->slug] = $term->name; 
    	// }

     //    // 
     //    $ticket_status = array('' => 'Select a Status');
     //    $terms = get_terms( 'status', array('hide_empty' => 0) );
     //    foreach($terms as $term){
     //        $ticket_status[$term->slug] = $term->name;    
     //    }


    	// $site_pages = get_pages();
    	// $pages = array();
    	// foreach($site_pages as $page){
    	// 	$pages[$page->ID] = $page->post_title;
    	// }
    	
        $archive_views = array(
            'calendar' => 'Monthly Calendar',
            'upcoming' => 'Upcoming Events',
            'archive' => 'Monthly Archive'
        );

    	$sections = array(
    		'base_section' => array(
    			'section' => array('page' => 'base_settings', 'title' => 'General Settings', 'description' => false),
    			'fields' => array(
    				array('type' => 'select', 'id' => 'event_archive_view', 'section' => 'base_section', 'setting_id' => 'jce_config', 'label' => 'Event Archive View', 'choices' => $archive_views, 'value' => ''),
		    	)
    		)/*,
			'ticket_section' => array(
    			'section' => array('page' => 'base_settings', 'title' => 'Ticket Settings', 'description' => 'General Ticket Settings'),
    			'fields' => array(
    				array('type' => 'select', 'id' => 'default_group', 'section' => 'ticket_section', 'setting_id' => 'support_system_config', 'label' => 'Default Unassigned Group', 'choices' => $support_groups, 'value' => ''),
                    array('type' => 'select', 'id' => 'ticket_open_status', 'section' => 'ticket_section', 'setting_id' => 'support_system_config', 'label' => 'Ticket Opened Status', 'choices' => $ticket_status, 'value' => ''),
                    array('type' => 'select', 'id' => 'ticket_close_status', 'section' => 'ticket_section', 'setting_id' => 'support_system_config', 'label' => 'Ticket Closed Status', 'choices' => $ticket_status, 'value' => ''),
                    array('type' => 'select', 'id' => 'ticket_responded_status', 'section' => 'ticket_section', 'setting_id' => 'support_system_config', 'label' => 'Ticket Team Reply Status', 'choices' => $ticket_status, 'value' => ''),
                    array('type' => 'select', 'id' => 'ticket_reply_status', 'section' => 'ticket_section', 'setting_id' => 'support_system_config', 'label' => 'Ticket Author Reply Status', 'choices' => $ticket_status, 'value' => ''),
		    	)
    		),
            'theme_section' => array(
                'section' => array( 'page' => 'base_settings', 'title' => 'Theme Settings', 'description' => 'Theme Settings'),
                'fields' => array(
                    array( 'type' => 'select' , 'id' => 'disable_css', 'section' => 'theme_section', 'setting_id' => 'support_system_config', 'label' => 'Disable Plugin CSS', 'value' => '', 'choices' => array('No', 'Yes'))
                )
            ),*/
            // email notifications tab
            /*'notification_override' => array(
                'section' => array('page' => 'notification_settings', 'title' => 'Notification Overrides', 'description' => 'Override templates with messages below'),
                'fields' => array(
                    array('type' => 'select', 'id' => 'override_admin', 'section' => 'notification_override', 'setting_id' => 'notification_override', 'label' => 'Override Admin Email', 'value' => '', 'choices' => array('no' => 'No', 'yes' => 'Yes')),
                    array('type' => 'select', 'id' => 'override_member', 'section' => 'notification_override', 'setting_id' => 'notification_override', 'label' => 'Override Member Email', 'value' => '', 'choices' => array('no' => 'No', 'yes' => 'Yes')),
                    array('type' => 'select', 'id' => 'override_public', 'section' => 'notification_override', 'setting_id' => 'notification_override', 'label' => 'Override Public Email', 'value' => '', 'choices' => array('no' => 'No', 'yes' => 'Yes')),
                )
            ),
            'notification_admin' => array(
                'section' => array('page' => 'notification_settings', 'title' => 'Admin Notification', 'description' => 'Notification email sent to admins once a ticket has been submitted.'),
                'fields' => array(
                    array('type' => 'text', 'id' => 'msg_title', 'section' => 'notification_admin', 'setting_id' => 'notification_admin', 'label' => 'Response Subject', 'value' => ''),
                    array('type' => 'textarea', 'id' => 'msg_body', 'section' => 'notification_admin', 'setting_id' => 'notification_admin', 'label' => 'Response Message', 'value' => ''),
                )
            ),
    		'notification_user' => array(
    			'section' => array('page' => 'notification_settings', 'title' => 'Member Notification', 'description' => 'Confirmation email sent to member once a ticket has been submitted.'),
    			'fields' => array(
    				array('type' => 'text', 'id' => 'msg_title', 'section' => 'notification_user', 'setting_id' => 'notification_user', 'label' => 'Response Subject', 'value' => ''),
    				array('type' => 'textarea', 'id' => 'msg_body', 'section' => 'notification_user', 'setting_id' => 'notification_user', 'label' => 'Response Message', 'value' => ''),
    			)
    		),
            'notification_public' => array(
                'section' => array('page' => 'notification_settings', 'title' => 'Public Notification', 'description' => 'Confirmation email sent to public user once a ticket has been submitted.'),
                'fields' => array(
                    array('type' => 'text', 'id' => 'msg_title', 'section' => 'notification_public', 'setting_id' => 'notification_public', 'label' => 'Response Subject', 'value' => ''),
                    array('type' => 'textarea', 'id' => 'msg_body', 'section' => 'notification_public', 'setting_id' => 'notification_public', 'label' => 'Response Message', 'value' => ''),
                )
            ),*/
    	);

    	$sections = array_merge($sections, apply_filters( 'jce/settings_sections', $sections));
    	$this->settings_sections = $sections;
    }

    /**
     * Generate the output for all settings fields
     * 
     * @param  array $args options for each field
     * @return void
     */
    public function field_callback($args)
    {
    	$value = '';
        $multiple = false;
        extract($args);
        $options = get_option($setting_id);
        $value = isset($options[$field_id]) ? $options[$field_id] : $value;
        switch($args['type'])
        {
            case 'text':
            {
                ?>
                <input class='text' type='text' id='<?php echo $setting_id; ?>-<?php echo $field_id; ?>' name='<?php echo $setting_id; ?>[<?php echo $field_id; ?>]' value='<?php echo $value; ?>' />
                <?php
                break;
            }
            case 'textarea':
            {
                ?>
                <textarea id='<?php echo $setting_id; ?>-<?php echo $field_id; ?>' name='<?php echo $setting_id; ?>[<?php echo $field_id; ?>]'><?php echo $value; ?></textarea>
                <?php
                break;
            }
            case 'select':
            {
                ?>
                <select id="<?php echo $setting_id; ?>" name="<?php echo $setting_id; ?>[<?php echo $field_id; ?>]<?php if($multiple === true): ?>[]<?php endif; ?>" <?php if($multiple === true): ?>multiple<?php endif; ?>>
                <?php
                foreach($choices as $id => $name):?>
                    <?php if(isset($value) && ((is_array($value) && in_array($id,$value)) || (!is_array($value) && $value == $id))): ?>
                    <option value="<?php echo $id; ?>" selected="selected"><?php echo $name; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select>
                <input type="hidden" name="<?php echo $setting_id; ?>[<?php echo $field_id; ?>_check]" value="1" />
                <?php
                break;
            }
            case 'upload':
            {
                ?>
                <input class='file' type='file' id='<?php echo $setting_id; ?>' name='<?php echo $setting_id; ?>[<?php echo $field_id; ?>]'  />
                <?php
                break;
            }
            case 'password':
            {
                ?>
                <input class='text' type='password' id='<?php echo $setting_id; ?>' name='<?php echo $setting_id; ?>[<?php echo $field_id; ?>]' value='<?php echo $value; ?>' />
                <?php
                break;
            }
        }
    }

    public function section_callback($args = ''){

        if($this->settings_sections[$args['id']]['section']['description'])
    		echo '<p>'.$this->settings_sections[$args['id']]['section']['description'].'</p>';
	}
}

new JCE_Admin_Settings();