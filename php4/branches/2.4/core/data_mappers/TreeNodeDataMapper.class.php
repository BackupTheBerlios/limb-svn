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
  function load(&$record, &$site_object)
  {
    $site_object->setNodeId($record->get('node_id'));
    $site_object->setParentNodeId($record->get('parent_node_id'));
    $site_object->setIdentifier($record->get('identifier'));
  }

  function insert(&$site_object)
  {
    if(!($parent_node_id = $site_object->getParentNodeId()))
      return throw(new LimbException('tree parent node is empty'));

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$tree->canAddNode($parent_node_id))
      return throw(new LimbException('tree registering failed', array('parent_node_id' => $parent_node_id)));

    $gntr =& $this->_getIdentifierGenerator();

    if(!$identifier = $gntr->generate($site_object))
      return throw(new LimbException('failed to generate identifier'));

    $values['identifier'] = $identifier;

    if (!$node_id = $tree->createSubNode($parent_node_id, $values))
      return throw(new LimbException('could not create tree node'));

    $site_object->setIdentifier($identifier);
    $site_object->setNodeId($node_id);
  }

  function update(&$site_object)
  {
    if(!$site_object->getNodeId())
      return throw(new LimbException('node id not set'));

    if(!$site_object->getParentNodeId())
      return throw(new LimbException('parent node id not set'));

    $node_id = $site_object->getNodeId();
    $parent_node_id = $site_object->getParentNodeId();
    $identifier = $site_object->getIdentifier();

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
    include_once(LIMB_DIR . '/core/data_mappers/DefaultSiteObjectIdentifierGenerator.class.php');
    return new DefaultSiteObjectIdentifierGenerator();
  }

  function _isObjectMovedFromNode($parent_node_id, $node)
  {
    return ($node['parent_id'] != $parent_node_id);
  }

  function delete(&$site_object)
  {
    if (!$this->_canDeleteTreeNode($site_object))
      return;

    $this->_deleteTreeNode($site_object);
  }

  function _deleteTreeNode(&$site_object)
  {
    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $tree->deleteNode($site_object->getNodeId());
  }

  function _canDeleteTreeNode(&$site_object)
  {
    if(!$site_object->getNodeId())
      return throw(new LimbException('node id not set'));

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();

    return $tree->canDeleteNode($site_object->getNodeId());
  }
}

?>
