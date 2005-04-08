<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DAO.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

class CurrentEntityDAO
{
  function CurrentEntityDAO(){}

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();
    $entity =& $toolkit->getCurrentEntity();
    $record = new Dataspace();
    $record->import($entity->export());
    return $record;
  }
}

?>
