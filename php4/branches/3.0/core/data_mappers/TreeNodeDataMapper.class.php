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
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');

class TreeNodeDataMapper extends AbstractDataMapper
{
  function load(&$record, &$object)
  {
    $object->set('node_id', $record->get('node_id'));
    $object->set('parent_node_id', $record->get('parent_node_id'));
    $object->set('identifier', $record->get('identifier'));
  }

  function insert(&$object)
  {
    $gntr =& $this->_getIdentifierGenerator();

    if(!$identifier = $gntr->generate($object))
      return throw(new LimbException('failed to generate identifier'));

    $values['identifier'] = $identifier;

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$parent_node_id = $object->get('parent_node_id'))
    {
      if (!$node_id = $tree->createRootNode($values))
        return throw(new LimbException('could not create root tree node'));
    }
    else
    {
      if(!$tree->canAddNode($parent_node_id))
        return throw(new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id)));

      if (!$node_id = $tree->createSubNode($parent_node_id, $values))
        return throw(new LimbException('could not create tree node'));
    }

    $object->set('identifier', $identifier);
    $object->set('node_id', $node_id);
  }

  function update(&$object)
  {
    if(!$object->get('node_id'))
      return throw(new LimbException('node id not set'));

    if(!$object->get('parent_node_id'))
      return throw(new LimbException('parent node id not set'));

    $node_id = $object->get('node_id');
    $parent_node_id = $object->get('parent_node_id');
    $identifier = $object->get('identifier');

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $node = $tree->getNode($node_id);

    if ($this->_isObjectMovedFromNode($parent_node_id, $node))
    {
      if(!$tree->canAddNode($parent_node_id))
        return throw(new LimbException('new parent cant accept children',
                                array('parent_node_id' => $parent_node_id)));

      if (!$tree->moveTree($node_id, $parent_node_id))
      {
        return throw(new LimbException('could not move node',
          array(
            'node_id' => $node_id,
            'target_id' => $parent_node_id,
          )
        ));
      }
    }

    if ($identifier != $node['identifier'])
      return $tree->updateNode($node_id, array('identifier' => $identifier), true);

    return true;
  }

  function &_getIdentifierGenerator()
  {
    include_once(LIMB_DIR . '/core/data_mappers/DefaultObjectIdentifierGenerator.class.php');
    return new DefaultObjectIdentifierGenerator();
  }

  function _isObjectMovedFromNode($parent_node_id, $node)
  {
    return ($node['parent_id'] != $parent_node_id);
  }

  function delete(&$object)
  {
    if (!$this->_canDeleteTreeNode($object))
      return;

    $this->_deleteTreeNode($object);
  }

  function _deleteTreeNode(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $tree->deleteNode($object->get('node_id'));
  }

  function _canDeleteTreeNode(&$object)
  {
    if(!$object->get('node_id'))
      return throw(new LimbException('node id not set'));

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    return $tree->canDeleteNode($object->get('node_id'));
  }
}

?>
