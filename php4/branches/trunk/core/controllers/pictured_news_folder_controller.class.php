<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');

class pictured_news_folder_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'permissions_required' => 'r',
            'template_path' => '/pictured_news_folder/display.html'
        ),
        'admin_display' => array(
            'permissions_required' => 'rw',
            'template_path' => '/pictured_news_folder/admin_display.html'
        ),
        'create_news' => array(
            'permissions_required' => 'w',
            'template_path' => '/pictured_news_object/create.html',
            'action_path' => '/pictured_news/create_pictured_news_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => strings :: get('create_newsline', 'newsline'),
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'permissions_required' => 'w',
            'template_path' => '/site_object/edit.html',
            'action_path' => '/site_object/edit_action',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/edit.gif',
            'action_name' => strings :: get('edit_news_folder', 'newsline'),
        ),
    );
  }
}

?>
