<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: faq_folder_container_controller.class.php 1038 2005-01-20 10:32:19Z moltyaninov $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class detail_info_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/detail_info/display.html'
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
            'template_path' => '/site_object/delete.html',
            'action_path' => 'form_delete_site_object_action',
            'JIP' => true,
            'popup' => true,
            'icon' => 'delete',
            'action_name' => strings :: get('delete'),
        ),
    );
  }
}

?>