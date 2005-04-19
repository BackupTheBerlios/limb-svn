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

class BreadcrumbsDAO
{
  function BreadcrumbsDAO(){}

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();

    if(!count($ids = $this->_getObjectIds()))
      return new PagedArrayDataset(array());

    $dao =& $this->getObjectClassNamesDAO();
    $criteria = new SimpleConditionCriteria('sys_object.oid IN (' . implode(",", $ids) .')');
    $dao->addCriteria($criteria);

    $rs =& new SimpleDbDataset($dao->fetch());
    $class_names = $rs->getArray('oid');

    $uow =& $toolkit->getUOW();
    foreach($ids as $id)
    {
      $object = $uow->load($class_names[$id]['name'], $id);
      $objects[$id] = $object->export();
    }

    return new PagedArrayDataset($objects);
  }

  function _getObjectIds()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $path = '';
    $translator =& $this->getPath2IdTranslator();
    $path_elements = $uri->getPathElements();
    foreach($path_elements as $element)
    {
      if($element == "" ) continue;
      $path .= '/' . $element;
      if($id = $translator->toId($path))
        $ids[] = $id;
    }

    return $ids;
  }

  function & getPath2IdTranslator()
  {
    include_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
    $translator  = new Path2IdTranslator();
    return $translator;
  }

  function & getObjectClassNamesDAO()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDAO('ObjectsClassNamesDAO');
  }

}

?>