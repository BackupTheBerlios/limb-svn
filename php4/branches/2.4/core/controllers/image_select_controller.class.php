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

class image_select_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/image_select/display.html',
            'popup' => true,
            'trasaction' => false
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/full_edit.html',
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