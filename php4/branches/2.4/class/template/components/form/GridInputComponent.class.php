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
require_once(LIMB_DIR . '/class/template/components/form/InputFormElement.class.php');

class GridInputComponent extends InputFormElement
{
  protected $hash_id = 'node_id';

  public function getValue()
  {
    return $this->findParentByClass('list_component')->get($this->attributes['name']);
  }

  public function setValue($value)
  {
  }

  public function renderAttributes()
  {
    if (isset($this->attributes['hash_id']))
      $this->hash_id = $this->attributes['hash_id'];

    unset($this->attributes['hash_id']);

    parent :: renderAttributes();
  }

  protected function _processNameAttribute($value)
  {
    $list = $this->findParentByClass('list_component');

    return 'grid_form' . $this->_makeIndexName($value) . '[' . $list->get($this->hash_id) . ']';
  }

}

?>