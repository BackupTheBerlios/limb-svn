<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class stats_event_controller extends site_object_controller
{
  function _define_default_action()
  {
    return 'events_list';
  }

  function _define_actions()
  {
    return array(
        'events_list' => array(
            'template_path' => '/stats_event/events_list.html',
            'action_name' => strings :: get('show_events_list', 'stats'),
            'action_path' => '/stats_event/stats_event_filter_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'icon' => 'edit'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'icon' => 'delete'
        ),
    );
  }
}

?>
