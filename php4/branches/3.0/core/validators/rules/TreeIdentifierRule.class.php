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
require_once(WACT_ROOT . '/validation/rule.inc.php');

define('TREE_IDENTIFIER_RULE_UNKNOWN_NODE_ID', -1000);

class TreeIdentifierRule extends SingleFieldRule
{
  var $parent_node_id;
  var $node_id;

  function TreeIdentifierRule($field_name, $parent_node_id, $node_id = TREE_IDENTIFIER_RULE_UNKNOWN_NODE_ID)
  {
    $this->node_id = $node_id;
    $this->parent_node_id = $parent_node_id;

    parent :: SingleFieldRule($field_name);
  }

  function check($value)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$tree->isNode($this->parent_node_id))
      return;

    $rs =& $tree->getChildren($this->parent_node_id);

    for($rs->rewind();$rs->valid();$rs->next())//$nodes as $id => $node)
    {
      $node = $rs->current();
      if($node->get('identifier') != $value)
        continue;

      if($node->get('id') != $this->node_id)
      {
        $this->error('INVALID');
        break;
      }
    }
  }
}

?>