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

class main_page_controller extends site_object_controller
{
  function _define_actions()
  {
    return array(
        'display' => array(
            'template_path' => '/main_page.html',
            'transaction' => false,
        ),
        'admin_display' => array(
            'template_path' => '/main_page/admin_display.html',
            'transaction' => false,
        ),
        'create_document' => array(
            'template_path' => '/document/create.html',
            'action_path' => '/document/create_document_action',
            'JIP' => true,
            'popup' => true,
            'action_name' => strings :: get('create_document', 'document'),
            'icon' => 'new.generic',
            'can_have_access_template' => true,
        ),
        'set_metadata' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('set_metadata'),
            'action_path' => '/site_object/set_metadata_action',
            'template_path' => '/site_object/set_metadata.html',
            'icon' => 'configure'
        ),
        'edit' => array(
            'popup' => true,
            'JIP' => true,
            'action_name' => strings :: get('edit'),
            'action_path' => '/main_page/edit_main_page_action',
            'template_path' => '/main_page/edit.html',
            'icon' => 'edit'
        ),
    );
  }
}

?>