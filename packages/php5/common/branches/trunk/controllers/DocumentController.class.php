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

class DocumentController extends SiteObjectController
{
  protected function _defineActions()
  {
    return array(
        'display' => array(
            'template_path' => '/document/display.html',
        ),
        'admin_display' => array(
            'template_path' => '/document/admin_display.html',
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
        'admin_detail' => array(
            'template_path' => '/admin/object_detail_info.html',
            'popup' => true,
            'JIP' => true,
            'img_src' => '/shared/images/admin_detail.gif',
            'action_name' => Strings :: get('detail_info'),
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
        'print_version' => array(
            'template_path' => '/document/print_version.html',
            'action_name' => Strings :: get('print_version_action', 'document'),
            'display_in_breadcrumbs' => false,
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => Strings :: get('edit_document', 'document'),
            'action_path' => '/document/edit_document_action',
            'template_path' => '/document/edit.html',
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
            'action_name' => Strings :: get('delete_document', 'document'),
            'action_path' => 'form_delete_site_object_action',
            'template_path' => '/site_object/delete.html',
            'img_src' => '/shared/images/rem.gif'
        ),
    );
  }
}

?>