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

class FaqFolderContainerController extends SiteObjectController
{
  function _defineActions()
  {
    return array(
        'display' => array(
            'template_path' => '/faq_folder_container/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/faq_folder_container/admin_display.html'
        ),
        'create_faq_folder' => array(
            'template_path' => '/faq_folder/create.html',
            'action_path' => '/faq_folder/create_faq_folder_action',
            'JIP' => true,
            'popup' => true,
            'img_src' => '/shared/images/new.folder.gif',
            'action_name' => Strings :: get('create_faq_folder','faq'),
            'can_have_access_template' => true,
        ),
        'publish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('publish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/publish.gif',
            'can_have_access_template' => true,
        ),
        'unpublish' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('unpublish'),
            'action_path' => '/doc_flow_object/set_publish_status_action',
            'img_src' => '/shared/images/unpublish.gif',
            'can_have_access_template' => true,
        ),
        'delete' => array(
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('delete_faq_folder','faq'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>