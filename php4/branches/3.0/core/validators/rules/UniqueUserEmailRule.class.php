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

class UniqueUserEmailRule extends SingleFieldRule
{
  var $current_email = '';

  function UniqueUserEmailRule($field_name, $email='')
  {
    $this->current_email = $email;

    parent :: SingleFieldRule($field_name);
  }

  function check($value)
  {
    if(	$this->current_email &&
        $this->current_email == $value)
      return;

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();

    $sql = 'SELECT *
            FROM user as tn
            WHERE tn.email=:email:';

    $stmt = $conn->newStatement($sql);
    $stmt->setVarChar('email', $value);
    $rs =& $stmt->getRecordSet();

    if($rs->getTotalRowCount() > 0)
      $this->error('ERROR_DUPLICATE_USER');
  }
}

?>