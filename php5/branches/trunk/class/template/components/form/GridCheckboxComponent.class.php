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
  public function getValue()
  {
    return $this->findParentByClass('list_component')->getByIndexString($this->_makeIndexName($this->attributes['name']));
  }

  public function setValue($value)
  {
  }

  protected function _processNameAttribute($value)
  {
    $list = $this->findParentByClass('list_component');

    return 'grid_form' . $this->_makeIndexName($value) . '[' . $list->get('node_id') . ']';
  }

}

?>