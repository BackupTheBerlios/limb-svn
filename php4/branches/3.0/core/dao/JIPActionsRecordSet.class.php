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
require_once(LIMB_DIR . '/core/permissions/JIPProcessor.class.php');

class JIPActionsRecordSet extends IteratorDbDecorator
{
  var $processor;

  function rewind()
  {
    parent :: rewind();

    $this->processor = new JIPProcessor();
  }

  function & current()
  {
    $record =& parent :: current();

    $this->processor->process($record);

    return $record;
  }
}

?>
