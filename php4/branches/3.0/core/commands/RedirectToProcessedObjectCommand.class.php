<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');

class RedirectToProcessedObjectCommand
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    if(!$mapped_object =& $toolkit->getProcessedObject())
      return LIMB_STATUS_ERROR;

    $path2id_translator =& $toolkit->getPath2IdTranslator();

    $path = $path2id_translator->toPath($mapped_object->get('oid'));
    $command =& $this->getRedirectCommand($path);
    return $command->perform();
  }

  function & getRedirectCommand($path)
  {
    return new RedirectCommand($path);
  }
}


?>