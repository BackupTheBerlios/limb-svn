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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

class news_folder_controller extends site_object_controller
{
  protected function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/news_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/news_folder/admin_display.html'
        ),
        'create_news' => array(
            'template_path' => '/news_object/create.html',
            'action_path' => '/news/create_news_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_newsline', 'newsline'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'template_path' => '/news_folder/edit.html',
            'action_path' => '/news_folder/edit_news_folder_action',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => strings :: get('edit_news_folder', 'newsline'),
        ),
    );
  }
}

?>