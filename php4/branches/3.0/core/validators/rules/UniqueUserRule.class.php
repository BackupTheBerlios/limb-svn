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
require_once(WACT_ROOT . '/validation/rule.inc.php');

class UniqueUserRule extends SingleFieldRule
{
  var $current_identifier = '';

  function UniqueUserRule($field_name, $current_identifier='')
  {
    $this->current_identifier = $current_identifier;

    parent :: SingleFieldRule($field_name);
  }

  function check($value)
  {
    if(	$this->current_identifier &&
        $this->current_identifier == $value)
      return;

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();

    $sql = 'SELECT *
            FROM user as tn
            WHERE tn.login=:login:';

    $stmt = $conn->newStatement($sql);
    $stmt->setVarChar('login', $value);
    $rs =& $stmt->getRecordSet();

    if($rs->getTotalRowCount() > 0)
      $this->error('ERROR_DUPLICATE_USER');
  }
}

?>