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

class NodeSiblingsDAO extends SQLBasedDAODecorator
{
  var $node_dao;

  function NodeSiblingsDAO(&$dao, &$node_dao)
  {
    $this->node_dao =& $node_dao;

    parent :: SQLBasedDAODecorator($dao);
  }

  function fetch()
  {
    $toolkit =& Limb :: toolkit();
    $record =& $this->node_dao->fetch();

    $criteria =& $this->getTreeNodeSiblingsCriteria();
    $criteria->setParentNodeId($record->get('_node_id'));

    $this->dao->addCriteria($criteria);

    return $this->dao->fetch();
  }

  function & getTreeNodeSiblingsCriteria()
  {
    include_once(LIMB_DIR . '/core/dao/criteria/TreeNodeSiblingsCriteria.class.php');
    return new TreeNodeSiblingsCriteria();
  }
}

?>
