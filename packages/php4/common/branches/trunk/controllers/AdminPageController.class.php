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

class AdminPageController extends SiteObjectController
{
  function _defineDefaultAction()
  {
    return 'admin_display';
  }

  function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/admin/admin_page.html',
            'transaction' => false,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'register_new_object' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('register_new_object'),
            'action_path' => '/site_object/register_new_object_action',
            'template_path' => '/site_object/register_new_object.html',
            'img_src' => '/shared/images/activate.gif'
        )
    );
  }
}

?>