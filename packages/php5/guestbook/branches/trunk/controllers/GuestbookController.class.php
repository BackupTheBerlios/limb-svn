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

class GuestbookController extends SiteObjectController
{
  protected function _defineActions()
  {
    return array(
        'display' => array(
            'action_path' => '/guestbook_message/front_create_guestbook_message_action',
            'template_path' => '/guestbook/display.html',
            'can_have_access_template' => true,
        ),
        'admin_display' => array(
            'template_path' => '/guestbook/admin_display.html'
        ),
        'create_guestbook_message' => array(
            'template_path' => '/guestbook_message/create.html',
            'action_path' => '/guestbook_message/create_guestbook_message_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => Strings :: get('create_message', 'guestbook'),
            'can_have_access_template' => true,
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