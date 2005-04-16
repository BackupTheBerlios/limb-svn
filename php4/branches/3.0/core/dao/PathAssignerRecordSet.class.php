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

class PathAssignerRecordSet extends IteratorDbDecorator
{
  function & current()
  {
    $record =& parent :: current();

    $toolkit =& Limb :: toolkit();

    $path2id_translator =& $toolkit->getPath2IdTranslator();
    $record->set('path', $path2id_translator->getPathToNode($record->get('_node_id')));

    return $record;
  }
}

?>
