<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Path2IdTranslator.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/

class Path2ServiceTranslator
{
  function & toService($path)
  {
    $to_id_translator =& $this->_getPath2IdTranslator();
    if(!$id = $to_id_translator->toId($path))
      return null;

    $toolkit = Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();

    $stmt =& $conn->newStatement('SELECT sys_service.name FROM sys_service, sys_service '.
                                 'WHERE sys_service.service_id = sys_service.id '.
                                 ' AND sys_service.oid = ' . $id);


    if ($name = $stmt->getOneValue())
    {
      include_once(LIMB_DIR . '/core/services/Service.class.php');
      return new Service($name);
    }
    else
      return null;
  }

  // for mocking
  function & _getPath2IdTranslator()
  {
    include_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
    return new Path2IdTranslator();
  }
}

?>
