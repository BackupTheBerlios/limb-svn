<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: unique_user_rule.class.php 431 2004-07-26 16:01:46Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

class unique_object_attribute_rule extends single_field_rule
{
  var $current_identifier = '';
  var $object_table = '';

  function unique_object_attribute_rule($field_name, $db_field_value, $object_table, $current_identifier='')
  {
    $this->current_identifier = $current_identifier;
    $this->object_table = $object_table;
    $this->db_field_value = $db_field_value;

    parent :: single_field_rule($field_name);
  }

  function validate(&$dataspace)
  {
    if(!$value = $dataspace->get($this->field_name))
      return;

    if(	$this->current_identifier &&
        $this->current_identifier == $value)
      return;

    $db =& db_factory :: instance();

    $sql = 'SELECT *
    FROM sys_site_object as sco, ' . $this->object_table. ' as tn
    WHERE tn.' . $this->db_field_value .'="' . $db->escape($value) . '"
    AND sco.id=tn.object_id
    AND sco.current_version=tn.version';

    $db->sql_exec($sql);

    $arr = $db->get_array();

    if(is_array($arr) && count($arr))
      $this->error(strings :: get('field_must_be_unique', 'error'));
  }
}

?>