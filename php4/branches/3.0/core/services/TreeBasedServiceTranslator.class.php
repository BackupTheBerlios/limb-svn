<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Service.class.php 1191 2005-03-25 14:04:13Z seregalimb $
*
***********************************************************************************/
class TreeBasedServiceTranslator // implements ServiceTranslator
{
  function & getService(&$request)
  {
    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $path2id_translator =& $toolkit->getPath2IdTranslator();

    if(!$id = $path2id_translator->toId($path))
      return null;

    $conn =& $toolkit->getDbConnection();
    $sql = 'SELECT name FROM sys_object, sys_class WHERE sys_object.class_id = sys_class.id
            AND sys_object.oid = '. $id;

    $stmt =& $conn->newStatement($sql);
    $class_name = $stmt->getOneValue();

    $uow =& $toolkit->getUOW();
    if($service =& $uow->load($class_name, $id))
      return $service->getService();
    else
      return null;
  }
}

?>