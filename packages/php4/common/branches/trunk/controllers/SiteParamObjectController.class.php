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
require_once(LIMB_DIR . '/class/core/controllers/SiteObjectController.class.php');

class SiteParamObjectController extends SiteObjectController
{
  protected function _defineDefaultAction()
  {
    return 'admin_display';
  }

  protected function _defineActions()
  {
    return array(
        'admin_display' => array(
            'template_path' => '/site_param_object/admin_display.html',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'update' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('set_params', 'site_param'),
            'action_path' => '/site_param_object/update_param_action',
            'template_path' => '/site_param_object/update.html',
            'img_src' => '/shared/images/details.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>