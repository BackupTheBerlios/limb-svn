<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/template/components/form/input_form_element.class.php');

class grid_input_component extends input_form_element
{
  var $hash_id = 'node_id';

  function get_value()
  {
    $list =& $this->find_parent_by_class('list_component');

    return $list->get($this->attributes['name']);
  }

  function set_value($value)
  {
  }

  function render_attributes()
  {
    if (isset($this->attributes['hash_id']))
      $this->hash_id = $this->attributes['hash_id'];

    unset($this->attributes['hash_id']);

    parent :: render_attributes();
  }

  function _process_name_attribute($value)
  {
    $list =& $this->find_parent_by_class('list_component');

    return 'grid_form' . $this->_make_index_name($value) . '[' . $list->get($this->hash_id) . ']';
  }

}

?>