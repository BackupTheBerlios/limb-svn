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
require_once(LIMB_DIR . '/core/commands/CreateSiteObjectCommand.class.php');

class CreatePollCommand extends CreateSiteObjectCommand
{
  function _defineSiteObjectClassName()
  {
    return 'poll';
  }
}

?>