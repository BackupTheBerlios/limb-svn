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

class Path2BehaviourTranslator
{
  function & toBehaviour($path)
  {
    $to_id_translator =& $this->_getPath2IdTranslator();
    if(!$id = $to_id_translator->toId($path))
      return null;

    $toolkit = Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();

    $stmt =& $conn->newStatement('SELECT sys_behaviour.name FROM sys_service, sys_behaviour '.
                                 'WHERE sys_service.behaviour_id = sys_behaviour.id '.
                                 ' AND sys_service.oid = ' . $id);


    if ($name = $stmt->getOneValue())
    {
      include_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');
      return new Behaviour($name);
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
