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
require_once(LIMB_DIR . '/class/core/actions/Action.class.php');

class TreeDisplayAction extends Action
{
  public function perform($request, $response)
  {
    $parents =& Limb :: toolkit()->getSession()->getReference('tree_expanded_parents');
    Limb :: toolkit()->getTree()->setExpandedParents($parents);
  }
}

?>