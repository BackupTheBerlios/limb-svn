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

class FileSelectController extends SiteObjectController
{
  function _defineActions()
  {
    return array(
        'display' => array(
            'template_path' => '/file_select/display.html',
            'popup' => true,
        ),
    );
  }
}

?>