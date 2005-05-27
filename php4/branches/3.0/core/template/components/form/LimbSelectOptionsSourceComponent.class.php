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
require_once(WACT_ROOT . '/template/components/list/list.inc.php');

class LimbSelectOptionsSourceComponent extends ListComponent
{
  function getChoices()
  {
    if(is_object($this->_datasource))
      return $this->_datasource->export();

    return $this->_exportDataSetAsChoices();
  }

  function _exportDataSetAsChoices()
  {
    $choices = array();

    for($this->dataSet->rewind(); $this->dataSet->valid(); $this->dataSet->next())
    {
      $record = $this->dataSet->current();
      $raw = $record->export();
      $choices[key($raw)] = current($raw);
    }

    return $choices;
  }
}
?>