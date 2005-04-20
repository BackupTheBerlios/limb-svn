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

class CurrentEntityDirectChildrenDAO extends SQLBasedDAODecorator
{
  function CurrentEntityDirectChildrenDAO(&$dao)
  {
    parent :: SQLBasedDAODecorator($dao);
  }

  function fetch()
  {
    $toolkit =& Limb :: toolkit();
    if(!$entity =& $toolkit->getCurrentEntity())
    {
      include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
      return new PagedArrayDataset(array());
    }

    if(!$node =& $entity->getPart('node'))
    {
      include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
      return new PagedArrayDataset(array());
    }

    $criteria =& $this->getTreeNodeSiblingsCriteria();
    $criteria->setParentNodeId($node->get('id'));

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
