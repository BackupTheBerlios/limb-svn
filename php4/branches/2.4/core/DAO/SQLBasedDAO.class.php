<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SQLBasedDAO.class.php 1073 2005-01-29 15:01:02Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

class SQLBasedDAO
{
  var $criterias = array();
  var $sql = null;

  function SQLBasedDAO()
  {
    $this->_initCriterias();
  }

  function setSQL(&$sql)
  {
    $this->sql =& $sql;
  }

  function & getSQL()
  {
    if($this->sql)
      return $this->sql;

    $this->sql =& $this->_initSQL();

    return $this->sql;
  }

  function addCriteria(&$criteria)
  {
    $this->criterias[] =& $criteria;
  }

  function & _initSQL(){}

  function & fetch()
  {
    $this->_processCriterias();

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();

    $sql =& $this->getSQL();
    $stmt =& $conn->newStatement($sql->toString());
    return $stmt->getRecordSet();
  }

  function _processCriterias()
  {
    $sql =& $this->getSQL();

    foreach(array_keys($this->criterias) as $key)
      $this->criterias[$key]->process($sql);
  }

  function _initCriterias(){}
}

?>
