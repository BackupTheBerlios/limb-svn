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

class TreeToggleAction extends Action
{
  public function perform($request, $response)
  {
    if($request->hasAttribute('recursive_search_for_node'))
      return;

    $parents =& Limb :: toolkit()->getSession()->getReference('tree_expanded_parents');
    Limb :: toolkit()->getTree()->setExpandedParents($parents);

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