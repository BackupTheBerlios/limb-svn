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

class ChildItemsPathAssignerRecordSet extends IteratorDbDecorator
{
  var $parent_path;

  function & current()
  {
    $record =& parent :: current();

    $record->set('path', $this->parent_path . '/' .$record->get('identifier'));

    return $record;
  }

  function rewind()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $id_translator = new Path2IdTranslator();
    if($path = $id_translator->toPath($request->get('id')))
      $this->parent_path = $path;

    parent :: rewind();
  }
}

?>
