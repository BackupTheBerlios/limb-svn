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
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');

class CurrentServiceNodeDAO
{
  function & fetch()
  {
    $toolkit =& Limb :: toolkit('service_node_toolkit');
    $locator =& $toolkit->getServiceNodeLocator();
    if(!$entity =& $locator->getCurrentServiceNode())
      return new DataSpace();

    $record =& new DataSpace();
    $record->import($entity->export());
    return $record;
  }
}

?>
