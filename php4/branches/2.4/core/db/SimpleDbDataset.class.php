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
require_once(LIMB_DIR . '/core/db/IteratorDbDecorator.class.php');

class SimpleDbDataset extends IteratorDbDecorator
{
  function getArray($index = null)
  {
    $arr = array();

    for($this->iterator->rewind();$this->iterator->valid();$this->iterator->next())
    {
      $record = $this->iterator->current();

      if(!is_null($index))
        $arr[$record->get($index)] = $record->export();
      else
        $arr[] = $record->export();
    }

    return $arr;
  }

  function getRow()
  {
    $this->iterator->rewind();
    $record = $this->iterator->current();
    return $record->export();
  }

  function getValue()
  {
    $this->iterator->rewind();
    $record = $this->iterator->current();
    if($arr = $record->export())
      return $arr[key($arr)];
  }
}

?>
