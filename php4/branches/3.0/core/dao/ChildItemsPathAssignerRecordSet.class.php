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

    if($this->parent_path)
      $record->set('path', $this->parent_path . '/' . $record->get('_node_identifier'));

    return $record;
  }

  function rewind()
  {
    $toolkit =& Limb :: toolkit();
    if($mapped_object =& $toolkit->getCurrentEntity())
    {

      $id_translator =& $toolkit->getPath2IdTranslator();
      if($path = $id_translator->toPath($mapped_object->get('oid')))
        $this->parent_path = $path;
    }

    parent :: rewind();
  }
}

?>
