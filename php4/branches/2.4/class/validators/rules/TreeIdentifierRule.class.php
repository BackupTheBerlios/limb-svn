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
require_once(LIMB_DIR . '/class/validators/rules/SingleFieldRule.class.php');

class TreeIdentifierRule extends SingleFieldRule
{
  const UNKNOWN_NODE_ID = -1000;

  var $parent_node_id;
  var $node_id;

  function TreeIdentifierRule($field_name, $parent_node_id, $node_id = TreeIdentifierRule :: UNKNOWN_NODE_ID)
  {
    $this->node_id = $node_id;
    $this->parent_node_id = $parent_node_id;

    parent :: SingleFieldRule($field_name);
  }

  function validate($dataspace)
  {
    if(!$value = $dataspace->get($this->field_name))
      return;

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$tree->isNode($this->parent_node_id))
      return;

    if(!$nodes = $tree->getChildren($this->parent_node_id))
      return;

    foreach($nodes as $id => $node)
    {
      if($node['identifier'] != $value)
        continue;

      if($this->node_id == TreeIdentifierRule :: UNKNOWN_NODE_ID)
      {
        $this->error(Strings :: get('error_duplicate_tree_identifier', 'error'));
        break;
      }
      elseif($id != $this->node_id)
      {
        $this->error(Strings :: get('error_duplicate_tree_identifier', 'error'));
        break;
      }
    }
  }
}

?>