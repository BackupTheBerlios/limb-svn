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
require_once(LIMB_DIR . '/class/template/components/form/InputCheckboxComponent.class.php');

class GridCheckboxComponent extends InputCheckboxComponent
{
  function getValue()
  {
    $cmp =& $this->findParentByClass('list_component');
    return $cmp->getByIndexString($this->_makeIndexName($this->attributes['name']));
  }

  function setValue($value)
  {
  }

  function _processNameAttribute($value)
  {
    $list = $this->findParentByClass('list_component');

    return 'grid_form' . $this->_makeIndexName($value) . '[' . $list->get('node_id') . ']';
  }

}

?>