<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SQLBasedDAO.class.php 1163 2005-03-15 16:31:45Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');

class SQLBasedDAODecorator extends SQLBasedDAO
{
  var $dao;

  function SQLBasedDAODecorator(&$dao)
  {
    $this->dao =& $dao;
  }

  function addCriteria(&$criteria)
  {
    $dao->addCriteria($criteria);
  }

  function & fetch()
  {
    return $this->dao->fetch();
  }

  function & fetchById($id)
  {
    return $this->dao->fetchById($id);
  }
}

?>
