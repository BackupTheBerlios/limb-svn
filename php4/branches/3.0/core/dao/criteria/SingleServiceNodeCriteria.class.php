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

class SingleServiceNodeCriteria
{
  var $path;
  var $node_id;
  var $object_id;

  function SingleServiceNodeCriteria()
  {
    $this->reset();
  }

  function setPath($path)
  {
    $this->path = $path;
  }

  function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  function setObjectId($object_id)
  {
    $this->object_id = $object_id;
  }

  function reset()
  {
    $this->path = '';
    $this->node_id = null;
    $this->object_id = null;
  }

  function _getNodeIdByPath()
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    $node = $tree->getNodeByPath($this->path);
    if (!$node)
      return null;
    else
      return $node['id'];
  }

  function process(&$sql)
  {
    if ($this->object_id)
    {
      $sql->addCondition('sys_object.oid = ' . $this->object_id);
      return;
    }

    if ($this->node_id)
    {
      $sql->addCondition('tree.id = ' . $this->node_id);
      return;
    }

    if ($this->path && $node_id = $this->_getNodeIdByPath())
    {
      $sql->addCondition('tree.id = ' . $node_id);
      return;
    }

    $sql->addCondition('0 = 1');
  }
}

?>