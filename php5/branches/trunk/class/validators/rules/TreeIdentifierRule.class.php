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

  protected $parent_node_id;
  protected $node_id;

  function __construct($field_name, $parent_node_id, $node_id = self :: UNKNOWN_NODE_ID)
  {
    $this->node_id = $node_id;
    $this->parent_node_id = $parent_node_id;

    parent :: __construct($field_name);
  }

  public function validate($dataspace)
  {
    if(!$value = $dataspace->get($this->field_name))
      return;

    $tree = Limb :: toolkit()->getTree();

    if(!$tree->isNode($this->parent_node_id))
      return;

    if(!$nodes = $tree->getChildren($this->parent_node_id))
      return;

    foreach($nodes as $id => $node)
    {
      if($node['identifier'] != $value)
        continue;

      if($this->node_id == self :: UNKNOWN_NODE_ID)
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