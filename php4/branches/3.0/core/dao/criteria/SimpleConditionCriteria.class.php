<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Criteria.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
class SimpleConditionCriteria
{
  var $condition;

  function SimpleConditionCriteria($condition)
  {
    $this->condition = $condition;
  }

  function process(&$sql)
  {
    $sql->addCondition($this->condition);
  }
}

?>
