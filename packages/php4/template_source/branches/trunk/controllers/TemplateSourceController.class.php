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

class TemplateSourceController extends SiteObjectController
{
  function _defineActions()
  {
    return array(
        'display' => array(
            'action_path' => '/template_source/display_template_source_action',
            'template_path' => '/template_source/display.html',
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