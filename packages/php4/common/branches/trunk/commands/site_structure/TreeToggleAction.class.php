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
require_once(LIMB_DIR . '/core/actions/Action.class.php');

class TreeToggleAction extends Action
{
  function perform(&$request, &$response)
  {
    if($request->hasAttribute('recursive_search_for_node'))
      return;

    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSesion();
    $tree =& $toolkit->getTree();

    $parents =& $session()->getReference('tree_expanded_parents');
    $tree->setExpandedParents($parents);

    if(!$id = $request->get('id'))
      $id = getMappedId();

    if($request->hasAttribute('expand'))
      $result = $tree->expandNode($id);
    elseif($request->hasAttribute('collapse'))
      $result = $tree->collapseNode($id);
    else
      $result = $tree->toggleNode($id);

    if(!$result)
      $request->setStatus(Request :: STATUS_FAILURE);
  }
}

?>