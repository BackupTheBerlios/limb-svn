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
require_once(LIMB_DIR . '/class/core/actions/action.class.php');

class tree_toggle_action extends action
{
  public function perform($request, $response)
  {
    if($request->has_attribute('recursive_search_for_node'))
      return;

    $parents =& Limb :: toolkit()->getSession()->get_reference('tree_expanded_parents');
    Limb :: toolkit()->getTree()->set_expanded_parents($parents);

    if(!$id = $request->get('id'))
      $id = get_mapped_id();

    if($request->has_attribute('expand'))
      $result = $tree->expand_node($id);
    elseif($request->has_attribute('collapse'))
      $result = $tree->collapse_node($id);
    else
      $result = $tree->toggle_node($id);

    if(!$result)
      $request->set_status(request :: STATUS_FAILURE);
  }
}

?>