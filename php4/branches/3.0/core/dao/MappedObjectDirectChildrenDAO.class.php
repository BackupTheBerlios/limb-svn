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
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAODecorator.class.php');

class MappedObjectDirectChildrenDAO extends SQLBasedDAODecorator
{
  function MappedObjectDirectChildrenDAO(&$dao)
  {
    parent :: SQLBasedDAODecorator($dao);
  }

  function fetch()
  {
    $toolkit =& Limb :: toolkit();
    if(!$mapped_object =& $toolkit->getMappedObject())
    {
      include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
      return new PagedArrayDataset(array());
    }

    $tree =& $toolkit->getTree();
    if(!$path = $tree->getPathToNode($mapped_object->get('node_id')))
    {
      include_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
      return new PagedArrayDataset(array());
    }

    $critetia =& $this->getTreeBranchCriteria();
    $critetia->setPath($path);

    $this->dao->addCriteria($critetia);

    include_once(LIMB_DIR . '/core/DAO/ChildItemsPathAssignerRecordSet.class.php');
    return new ChildItemsPathAssignerRecordSet($this->dao->fetch());
  }

  function & getTreeBranchCriteria()
  {
    include_once(LIMB_DIR . '/core/DAO/criteria/TreeBranchCriteria.class.php');
    return new TreeBranchCriteria();
  }
}

?>
