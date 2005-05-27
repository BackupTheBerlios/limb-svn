<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsRootGroupTest.class.php 1128 2005-03-02 15:07:18Z seregalimb $
*
***********************************************************************************/
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/dao/criteria/SimpleConditionCriteria.class.php');

class BreadcrumbsDAO // implements DAO
{
  function BreadcrumbsDAO(){}

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();

    if(!count($ids = $this->_getObjectIds()))
      return new PagedArrayDataset(array());

    $dao =& $toolkit->createDAO('ObjectsClassNamesDAO');
    $criteria = new SimpleConditionCriteria('sys_object.oid IN (' . implode(",", array_keys($ids)) .')');
    $dao->addCriteria($criteria);

    $rs =& new SimpleDbDataset($dao->fetch());
    $class_names = $rs->getArray('oid');

    $uow =& $toolkit->getUOW();
    foreach($ids as $id => $path)
    {
      $object = $uow->load($class_names[$id]['name'], $id);
      $objects[$id] = $object->export();
      $objects[$id]['_node_path'] = $path;
    }

    return new PagedArrayDataset($objects);
  }

  function _getObjectIds()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $path = '';
    $translator =& $toolkit->getPath2IdTranslator();
    $path_elements = $uri->getPathElements();

    $ids = array();

    foreach($path_elements as $element)
    {
      if($element == "" ) continue;
      $path .= '/' . $element;
      if($id = $translator->toId($path))
        $ids[$id] = $path;
    }

    return $ids;
  }
}

?>