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

class RedirectToParentObjectCommand
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    if(!$mapped_object =& $toolkit->getCurrentEntity())
      return LIMB_STATUS_ERROR;

    $db_table =& $toolkit->createDBTable('SysObject2Node');
    $rs =& $db_table->select(array('node_id' =>  $mapped_object->get('parent_node_id')));
    $rs->rewind();

    if($rs->valid())
    {
      $record =& $rs->current();
      $path2id_translator =& $toolkit->getPath2IdTranslator();
      $path = $path2id_translator->toPath((integer)$record->get('oid'));
      $command =& $this->getRedirectCommand($path);
    }
    else
      $command =& $this->getRedirectCommand('/');

    return $command->perform();
  }

  function & getRedirectCommand($path)
  {
    return new RedirectCommand($path);
  }
}


?>
