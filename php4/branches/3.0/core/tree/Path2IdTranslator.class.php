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
define('PATH_TO_ID', 'path_to_id_group');

class Path2IdTranslator
{
  var $ext_offset = '';
  var $int_offset = '';

  function setExternalOffset($offset)
  {
    $this->ext_offset = rtrim($offset, '/');
  }

  function setInternalOffset($offset)
  {
    $this->int_offset = rtrim($offset, '/');
  }

  function _applyPathOffsets($path)
  {
    if($this->ext_offset)
      $path = str_replace($this->ext_offset, '', $path);

    if($this->int_offset)
      $path = $this->int_offset . $path;

    return $path;
  }

  function toId($path)
  {
    $path = $this->_applyPathOffsets($path);

    $toolkit = Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$node = $tree->getNodeByPath($path))
      return null;

    $table =& $toolkit->createDBTable('SysObject2Node');
    $rs =& $table->select(array('node_id' => $node['id']));

    if(!$row = $rs->getRow())
      return null;

    return $row['oid'];
  }

  function toPath($id)
  {
    $toolkit = Limb :: toolkit();

    $table =& $toolkit->createDBTable('SysObject2Node');
    $rs =& $table->select(array('oid' => $id));

    if(!$row = $rs->getRow())
      return null;

    $tree =& $toolkit->getTree();

    if(!$path = $tree->getPathToNode((integer)$row['node_id']))
       return null;

    return $this->_applyResultingPathOffsets($path);
  }

  function getPathToObject(&$object)
  {
    if ($parent_node_id = $object->get('parent_node_id'))
    {
      $path = $this->_getPathByNode($parent_node_id);
      return $path . '/' . $object->get('identifier');
    }

    if($node_id = $object->get('node_id'))
      return $this->_getPathByNode($node_id);

    return $this->toPath($object->get('oid'));
  }

  function _getPathByNode($node_id)
  {
    $toolkit =& Limb :: toolkit();
    $cache =& $toolkit->getCache();

    if(!$path = $cache->get($node_id, PATH_TO_ID))
    {
      $tree =& $toolkit->getTree();
      $path = $tree->getPathToNode($node_id);
      $path = $this->_applyResultingPathOffsets($path);
      $cache->put($node_id, $path, PATH_TO_ID);
    }

    return $path;
  }

  function _applyResultingPathOffsets($path)
  {
    $path = str_replace($this->int_offset, '', $path);
    return $this->ext_offset . $path;
  }
}

?>
