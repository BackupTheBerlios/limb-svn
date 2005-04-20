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
require_once(LIMB_DIR . '/core/dao/processors/PathRecordProcessor.class.php');

class PathAssignerRecordSet extends IteratorDbDecorator
{
  var $processor;

  function PathAssignerRecordSet(&$iterator)
  {
    parent :: IteratorDbDecorator($iterator);

    $this->processor = new PathRecordProcessor();
  }

  function & current()
  {
    $record =& parent :: current();

    $this->processor->process($record);

    return $record;
  }
}

?>
