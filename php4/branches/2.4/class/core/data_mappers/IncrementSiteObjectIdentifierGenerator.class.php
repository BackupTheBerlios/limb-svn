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

class IncrementSiteObjectIdentifierGenerator// implements SiteObjectIdentifierGenerator
{
  function generate($site_object)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $identifier = $tree->getMaxChildIdentifier($site_object->getParentNodeId());

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
