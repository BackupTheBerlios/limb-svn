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
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

class ObjectsClassNamesDAO extends SQLBasedDAO
{
  function _initSQL()
  {
    $sql = new ComplexSelectSQL('SELECT sys_class.name, sys_object.oid as oid %fields% ' .
                                ' FROM sys_object, sys_class %tables% %left_join% '.
                                ' WHERE sys_object.class_id = sys_class.id  %where% %order% %group%');

    return $sql;
  }
}

?>
