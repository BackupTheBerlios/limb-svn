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
require_once(LIMB_DIR . '/class/validators/rules/SingleFieldRule.class.php');

class UniqueUserRule extends SingleFieldRule
{
  var $current_identifier = '';

  function UniqueUserRule($field_name, $current_identifier='')
  {
    $this->current_identifier = $current_identifier;

    parent :: SingleFieldRule($field_name);
  }

  function validate($dataspace)
  {
    if(!$value = $dataspace->get($this->field_name))
      return;

    if(	$this->current_identifier &&
        $this->current_identifier == $value)
      return;

    $toolkit =& Limb :: toolkit();
    $db =& $toolkit->getDB();

    $sql = 'SELECT *
            FROM sys_site_object as sco, user as tn
            WHERE sco.identifier="' . $db->escape($value) . '"
            AND sco.id=tn.object_id
            AND sco.current_version=tn.version';

    $db->sqlExec($sql);

    $arr = $db->getArray();

    if(is_array($arr) &&  count($arr))
      $this->error(Strings :: get('error_duplicate_user', 'error'));
  }
}

?>