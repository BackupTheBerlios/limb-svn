<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/ComplexSelectSQL.class.php');

class SQLBasedDAO
{
  var $criterias = array();

  function SQLBasedDAO()
  {
    $this->_initCriterias();
  }

  function addCriteria(&$criteria)
  {
    $this->criterias[] =& $criteria;
  }

  function & _getConnection()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->getDbConnection();
  }

  function & _initSQL(){}

  function & _defineIdName()
  {
    return 'oid';
  }

  function & fetch()
  {
    $sql =& $this->_initSQL();

    $this->_processCriterias($sql);

    $conn =& $this->_getConnection();
    $stmt =& $conn->newStatement($sql->toString());
    return $stmt->getRecordSet();
  }

  function & fetchById($id)
  {
    $sql =& $this->_initSQL();

    $this->_processCriterias($sql);

    $sql->addCondition($this->_defineIdName() . '=' . (int)$id);

    $conn =& $this->_getConnection();
    $stmt =& $conn->newStatement($sql->toString());

    $rs =& $stmt->getRecordSet();
    $rs->rewind();
    if(!$rs->valid())
      return null;

    return $rs->current();
  }

  function _processCriterias(&$sql)
  {
    foreach(array_keys($this->criterias) as $key)
    {
      $criteria =& Handle :: resolve($this->criterias[$key]);
      $criteria->process($sql);
    }
  }

  function _initCriterias(){}
}

?>
