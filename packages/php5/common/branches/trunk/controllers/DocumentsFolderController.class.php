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

class DocumentsFolderController extends SiteObjectController
{
  protected function _defineActions()
  {
    return array(
        'display' => array(
            'template_path' => '/documents_folder/display.html'
        ),
        'admin_display' => array(
            'template_path' => '/documents_folder/admin_display.html',
            'action_name' => Strings :: get('admin_display'),
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'img_src' => '/shared/images/configure.gif'
        ),
        'create_document' => array(
            'template_path' => '/document/create.html',
            'action_path' => '/document/create_document_action',
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('create_document', 'document'),
            'img_src' => '/shared/images/new.generic.gif',
            'can_have_access_template' => true,
        ),
        'create_documents_folder' => array(
            'template_path' => '/documents_folder/create.html',
            'action_path' => '/documents_folder/create_documents_folder_action',
            'JIP' => true,
            'popup' => true,
            'action_name' => Strings :: get('create_document_folder', 'document'),
            'img_src' => '/shared/images/new.folder.gif',
            'can_have_access_template' => true,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit'),
            'action_path' => '/site_object/edit_action',
            'template_path' => '/site_object/edit.html',
            'img_src' => '/shared/images/edit.gif'
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
            'action_name' => Strings :: get('delete'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>