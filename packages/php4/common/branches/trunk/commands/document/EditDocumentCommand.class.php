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
require_once(LIMB_DIR . '/core/commands/EditSiteObjectCommand.class.php');

class EditDocumentCommand extends EditSiteObjectCommand
{
  function _defineSiteObjectClassName()
  {
    return 'document';
  }
}

?>