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

class StatsSearchReportsPercentageRecordSet extends IteratorDbDecorator
{
  var $total_hits = 0;

  function & current()
  {
    $record =& parent :: current();

    if($this->total_hits)
      $record->set('percentage', round($record->get('hits') / $this->total_hits * 100, 2));
    else
      $record->set('percentage', 0);

    return $record;
  }

  function rewind()
  {
    parent :: rewind();

    $this->_getTotalHits();
  }

  function _getTotalHits()
  {
    $toolkit =& Limb :: toolkit();
    $dao =& $toolkit->createDAO('StatsSearchEnginesHitsReportDAO');

    $rs =& $dao->fetch();

    $this->total_hits = $rs->getTotalRowCount();
  }
}

?>
