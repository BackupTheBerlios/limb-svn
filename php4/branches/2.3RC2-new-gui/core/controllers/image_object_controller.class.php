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

class image_object_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'action_path' => '/images/display_image_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/images/edit_image_action',
            'template_path' => '/image/edit.html',
            'img_src' => '/shared/images/actions/edit.gif'
        ),
        'edit_variations' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit_variations', 'image'),
            'action_path' => '/images/edit_variations_action',
            'template_path' => '/image/edit_variations.html',
            'img_src' => '/shared/images/actions/look_group.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/actions/delete.gif',
            'action_name' => strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
        ),
    );
  }
}

?>