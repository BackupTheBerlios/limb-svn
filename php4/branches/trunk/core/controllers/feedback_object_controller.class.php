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
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class feedback_object_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'action_path' => '/feedback_object/send_feedback_action',
            'template_path' => '/feedback_object/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/feedback_object/admin_display.html'
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_feedback_content', 'feedback'),
            'action_path' => '/feedback_object/edit_feedback_action',
            'template_path' => '/feedback_object/edit.html',
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