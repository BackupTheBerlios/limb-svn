<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/controllers/SiteObjectController.class.php');

class ClassFolderController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'admin_display';
  }

  function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/class_folder/admin_display.html',
        ),
        'set_group_access' => array(
            'template_path' => '/class_folder/set_group_access.html',
            'action_path' => '/class_folder/set_group_access',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/access_manage.gif',
            'action_name' => Strings :: get('set_group_access'),
        ),
        'set_group_access_template' => array(
            'template_path' => '/class_folder/set_group_access_template.html',
            'action_path' => '/class_folder/set_group_access_template_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/access_template_manage.gif',
            'action_name' => Strings :: get('set_group_access_template'),
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
    );
  }
}

?>