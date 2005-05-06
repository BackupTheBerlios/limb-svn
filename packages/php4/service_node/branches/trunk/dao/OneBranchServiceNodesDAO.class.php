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
require_once(LIMB_DIR . '/core/dao/SQLBasedDAODecorator.class.php');
require_once(LIMB_DIR . '/core/dao/SQLBasedDAODecorator.class.php');

class OneBranchServiceNodesDAO extends SQLBasedDAODecorator
{
  function OneBranchServiceNodesDAO()
  {
    $toolkit =& Limb :: toolkit();
    $service_node_dao =& $toolkit->createDAO('ServiceNodeDAO');

    parent :: SQLBasedDAODecorator($service_node_dao);
  }

  function fetch()
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver('service_node');

    if(!is_object($resolver))
      return new PagedArrayDataSet(array());

    if(!$entity =& $resolver->resolve($toolkit->getRequest()))
      $this->_addTreeRootNodesCriteria();
    else
      $this->_addTreeBranchCriteria($entity);

    return $this->dao->fetch();
  }

  function _addTreeRootNodesCriteria()
  {
    include_once(LIMB_DIR . '/core/dao/criteria/TreeRootNodesCriteria.class.php');
    $criteria = new TreeRootNodesCriteria();
    $this->dao->addCriteria($criteria);
  }

  function _addTreeBranchCriteria(&$entity)
  {
    $toolkit =& Limb :: toolkit();

    $node =& $entity->getNodePart();

    include_once(LIMB_DIR . '/core/dao/criteria/TreeNodeSiblingsCriteria.class.php');
    $criteria = new TreeNodeSiblingsCriteria();
    $criteria->setParentNodeId($node->get('id'));

    $this->dao->addCriteria($criteria);
  }
}

?>
