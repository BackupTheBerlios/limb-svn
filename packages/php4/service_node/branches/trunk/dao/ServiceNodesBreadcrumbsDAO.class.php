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
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(LIMB_DIR . '/core/dao/criteria/SimpleConditionCriteria.class.php');

class ServiceNodesBreadcrumbsDAO
{
  function ServiceNodesBreadcrumbsDAO(){}

  function & fetch()
  {
    $toolkit =& Limb :: toolkit();
    $service_node_toolkit =& Limb :: toolkit('service_node_toolkit');
    $locator =& $service_node_toolkit->getServiceNodeLocator();

    if(!$entity =& $locator->getCurrentServiceNode())
      return new PagedArrayDataset(array());

    $tree =& $toolkit->getTree();
    $node =& $entity->getPart('node');
    if(!$parents =& $tree->getParents($node->get('id')))
      return new PagedArrayDataset(array());

    for($parents->rewind(); $parents->valid(); $parents->next())
    {
      $record =& $parents->current();
      $ids[] =& $record->get('id');
    }

    $ids[] = $node->get('id');

    $dao =& $toolkit->createDAO('ServiceNodeDAO');
    $dao->addCriteria(new SimpleConditionCriteria('tree.id IN ('. implode(',', $ids) . ')'));
    return $dao->fetch();
  }
}

?>
