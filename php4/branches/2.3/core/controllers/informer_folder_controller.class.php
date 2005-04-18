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

class informer_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/informer_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/informer_folder/admin_display.html'
        ),
        'create_informer' => array(
            'template_path' => '/informer_object/create.html',
            'action_path' => '/informer_object/create_informer_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_informer', 'informer'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>