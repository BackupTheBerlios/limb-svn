<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/site_object_identifier_generator.interface.php');

class IncrementSiteObjectIdentifierGenerator implements SiteObjectIdentifierGenerator 
{
  public function generate($site_object)
  {
    $tree = Limb :: toolkit()->getTree();
    $identifier = $tree->get_max_child_identifier($site_object->get_parent_node_id());
                                                  
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
