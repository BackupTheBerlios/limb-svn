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
require_once(LIMB_DIR . '/class/controllers/SiteObjectController.class.php');

class PollContainerController extends SiteObjectController
{
  function _defineActions()
  {
    return array(
        'display' => array(
            'template_path' => '/poll_container/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/poll_container/admin_display.html'
        ),
        'create_poll' => array(
            'template_path' => '/poll/create.html',
            'action_path' => '/poll/create_poll_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.generic.gif',
            'action_name' => Strings :: get('create_poll_question','poll'),
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
        'vote' => array(
            'action_path' => '/poll_container/vote_action',
            'template_path' => '/poll_container/display.html'
        ),
    );
  }
}

?>