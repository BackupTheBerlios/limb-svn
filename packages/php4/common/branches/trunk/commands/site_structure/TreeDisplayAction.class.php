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
require_once(LIMB_DIR . '/class/actions/Action.class.php');

class TreeDisplayAction extends Action
{
  function perform(&$request, &$response)
  {
    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSesion();
    $tree =& $toolkit->getTree();

    $parents =& $session()->getReference('tree_expanded_parents');
    $tree->setExpandedParents($parents);
  }
}

?>