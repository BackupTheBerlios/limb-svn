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
  protected $current_identifier = '';

  function __construct($field_name, $current_identifier='')
  {
    $this->current_identifier = $current_identifier;

    parent :: __construct($field_name);
  }

  public function validate($dataspace)
  {
    if(!$value = $dataspace->get($this->field_name))
      return;

    if(	$this->current_identifier && 
        $this->current_identifier == $value)
      return;

    $db = Limb :: toolkit()->getDB();

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