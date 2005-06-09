<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: IncrementObjectIdentifierGenerator.class.php 1103 2005-02-14 15:16:43Z pachanga $
*
***********************************************************************************/

class IncrementObjectIdentifierGenerator// implements ObjectIdentifierGenerator
{
  function generate(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $identifier = $tree->getMaxChildIdentifier($object->get('parent_node_id'));

    if($identifier === false)
      return false;

    if(preg_match('/(.*?)(\d+)$/', $identifier, $matches))
      $new_identifier = $matches[1] . ($matches[2] + 1);
    else
      $new_identifier = $identifier . '1';

    return $new_identifier;
  }
}

?>
