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
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

class template_source_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'permissions_required' => 'r',
            'action_path' => '/template_source/display_template_source_action',
            'template_path' => '/template_source/display.html',
        ),
        'delete' => array(
            'permissions_required' => 'w',
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