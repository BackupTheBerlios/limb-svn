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
require_once(LIMB_DIR . '/core/Limb.class.php');

class UIDGenerator
{
  function next()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();

    $stmt = $conn->newStatement('SELECT id FROM sys_uid');
    if(!$uid = $stmt->getOneValue())
    {
      $uid = 1;
      $stmt = $conn->newStatement('INSERT INTO sys_uid (id) VALUES (:id:)');
      $stmt->setInteger('id', $uid);
    }
    else
    {
      $uid++;
      $stmt = $conn->newStatement('UPDATE sys_uid SET id=:id:');
      $stmt->setInteger('id', $uid);
    }

    $stmt->execute();
    return $uid;
  }
}

?>
