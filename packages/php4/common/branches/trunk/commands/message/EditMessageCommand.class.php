<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/EditSiteObjectCommand.class.php');

class EditMessageCommand extends EditSiteObjectCommand
{
  function _defineSiteObjectClassName()
  {
    return 'message';
  }
}

?>