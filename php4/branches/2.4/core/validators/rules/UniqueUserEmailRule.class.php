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
  var $current_identifier = '';

  function UniqueUserEmailRule($field_name, $current_identifier='')
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
            FROM sys_site_object as sco, user as tn
            WHERE tn.email=:email
            AND sco.id=tn.object_id
            AND sco.current_version=tn.version';

    $stmt = $conn->newStatement($sql);
    $stmt->setVarChar('email', $value);
    $rs =& $stmt->getRecordSet();

    if($rs->getTotalRowCount() > 0)
      $this->error('ERROR_DUPLICATE_USER');
  }
}

?>