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

class TreeBasedEntityRequestResolver
{
  function & resolve(&$request)
  {
    if(!$id = $this->_getId($request))
      return false;

    $toolkit =& Limb :: toolkit();

    $conn =& $toolkit->getDbConnection();
    $sql = 'SELECT sys_class.name FROM sys_object, sys_class
            WHERE sys_object.class_id = sys_class.id
            AND sys_object.oid = :id:';

    $stmt =& $conn->newStatement($sql);
    $stmt->setInteger('id', $id);

    if(!$class_name = $stmt->getOneValue())
      return false;

    $uow =& $toolkit->getUOW();
    return $uow->load($class_name, $id);
  }

  function _getId(&$request)
  {
    if($request->hasAttribute('oid'))
      return (int)$request->get('oid');

    $toolkit =& Limb :: toolkit();
    $uri =& $request->getUri();
    $path = $uri->getPath();

    $path2id_translator =& $toolkit->getPath2IdTranslator();

    return $path2id_translator->toId($path);
  }
}

?>