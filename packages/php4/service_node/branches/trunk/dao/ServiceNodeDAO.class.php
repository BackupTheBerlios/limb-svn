<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudDomainObjectDAO.class.php 27 2005-02-26 18:57:22Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/SQLBasedDAO.class.php');

class ServiceNodeDAO extends SQLBasedDAO
{
  function & _initSQL()
  {
    $sql = new ComplexSelectSQL('SELECT '.
                                  'sys_object.oid, '.
                                  'sys_class.name as class_name, '.
                                  'sys_class.id as class_id, '.
                                  'sys_object_to_service.title as _service_title, '.
                                  'sys_service.id as _service_id, '.
                                  'sys_service.name as _service_name, '.
                                  'tree.id as _node_id, '.
                                  'tree.parent_id as _node_parent_id, '.
                                  'tree.identifier as _node_identifier'.
                                  '%fields% '.
                                'FROM '.
                                  'sys_object, '.
                                  'sys_class, '.
                                  'sys_tree as tree, '.
                                  'sys_object_to_node, '.
                                  'sys_object_to_service, '.
                                  'sys_service '.
                                  ' %tables% %left_join% '.
                                'WHERE '.
                                  'sys_class.id = sys_object.class_id '.
                                  'AND sys_object_to_node.node_id = tree.id ' .
                                  'AND sys_object_to_node.oid = sys_object.oid ' .
                                  'AND sys_object_to_service.oid = sys_object.oid '.
                                  'AND sys_object_to_service.service_id = sys_service.id '.
                                '%where% %order% %group%');

    return $sql;
  }

  function & _defineIdName()
  {
    return 'sys_object.oid';
  }
}

?>
