<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/actions/action.class.php');

class tree_toggle_action extends action
{
  function perform(&$request, &$response)
  {
    $tree =& tree :: instance();
    $tree->initialize_expanded_parents();

    if($request->has_attribute('recursive_search_for_node'))
      return;

    if(!$id = $request->get_attribute('id'))
      $id = get_mapped_id();

    if($request->has_attribute('expand'))
      $result = $tree->expand_node($id);
    elseif($request->has_attribute('collapse'))
      $result = $tree->collapse_node($id);
    else
      $result = $tree->toggle_node($id);

    if(!$result)
      $request->set_status(REQUEST_STATUS_FAILURE);
  }
}

?>