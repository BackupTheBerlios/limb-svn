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
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class ad_block_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/ad_block_folder/display.html',
            'popup' => true
        ),
        'admin_display' => array(
            'template_path' => '/ad_block_folder/admin_display.html',
            'action_name' => strings :: get('admin_display'),
        ),
        'create_ad_block' => array(
            'template_path' => '/ad_block_object/create.html',
            'action_path' => '/ad_block_object/create_ad_block_object_action',
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('create_ad_block', 'ad'),
            'img_src' => '/shared/images/actions/new.generic.gif',
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/actions/delete.gif'
        ),
    );
  }
}

?>