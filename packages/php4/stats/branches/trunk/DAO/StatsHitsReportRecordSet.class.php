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

class StatsHitsReportRecordSet extends IteratorDbDecorator
{
  var $array_dataset;

  function valid()
  {
    return $this->array_dataset->valid();
  }

  function next()
  {
    $this->array_dataset->next();
  }

  function & current()
  {
    return $this->array_dataset->current();
  }

  function rewind()
  {
    parent :: rewind();

    $this->_processRecordSet();

    $this->array_dataset->rewind();
  }

  function _processRecordSet()
  {
    $this->array_dataset = new ArrayDataset(array());

    $records = array();

    for($this->iterator->rewind(); $this->iterator->valid(); $this->iterator->next())
    {
      $record =& $this->iterator->current();
      $records[] =& $record->export();
    }

    $this->_findMaxValues($records);

    $this->array_dataset->importDataSetAsArray($records);
  }

  function _findMaxValues(&$arr)
  {
    include_once(LIMB_DIR . '/core/util/ComplexArray.class.php');

    if(ComplexArray :: getMaxColumnValue('hosts', $arr, $index) !== false)
      $arr[$index]['max_hosts'] = 1;

    if(ComplexArray :: getMaxColumnValue('hits', $arr, $index) !== false)
      $arr[$index]['max_hits'] = 1;

    if(ComplexArray :: getMaxColumnValue('home_hits', $arr, $index) !== false)
      $arr[$index]['max_home_hits'] = 1;

    if(ComplexArray :: getMaxColumnValue('audience', $arr, $index) !== false)
      $arr[$index]['max_audience'] = 1;

    foreach($arr as $index => $data)
    {
      if(date('w', $data['time'] + 60*60*24) == 1)
        $arr[$index]['new_week'] = 1;
    }
  }
}

?>
