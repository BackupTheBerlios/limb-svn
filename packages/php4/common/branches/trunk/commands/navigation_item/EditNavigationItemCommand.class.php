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
require_once(LIMB_DIR . '/class/commands/EditSiteObjectCommand.class.php');

class EditNavigationItemCommand extends EditSiteObjectCommand
{
  function _defineSiteObjectClassName()
  {
    return 'navigation_item';
  }

  function _defineIncreaseVersionFlag()
  {
    return false;
  }
}

?>