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

class ImageObjectController extends SiteObjectController
{
  function _defineActions()
  {
    return array(
        'display' => array(
            'action_path' => '/images/display_image_action',
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/images/edit_image_action',
            'template_path' => '/image/edit.html',
            'img_src' => '/shared/images/edit.gif'
        ),
        'edit_variations' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit_variations', 'image'),
            'action_path' => '/images/edit_variations_action',
            'template_path' => '/image/edit_variations.html',
            'img_src' => '/shared/images/look_group.gif'
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/rem.gif',
            'action_name' => Strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
        ),
    );
  }
}

?>