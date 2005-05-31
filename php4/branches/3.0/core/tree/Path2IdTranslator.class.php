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
define('ID_TO_PATH', 'id_to_path_group');

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

  function toId($raw_path)
  {
    $toolkit =& Limb :: toolkit();
    $cache =& $toolkit->getCache();

    if($cache->assign($id, $raw_path, ID_TO_PATH))
      return $id;

    $path = $this->_applyPathOffsets($raw_path);

    $toolkit = Limb :: toolkit();
    $tree =& $toolkit->getTree();

    if(!$node = $tree->getNodeByPath($path))
      return null;

    $table =& $toolkit->createDBTable('SysObject2Node');
    $rs =& $table->select(array('node_id' => $node['id']));

    if(!$row = $rs->getRow())
      return null;

    $oid = $row['oid'];
    $cache->put($raw_path, $oid, ID_TO_PATH);

    return $oid;
  }

  function toPath($id)
  {
    $toolkit = Limb :: toolkit();

    $table =& $toolkit->createDBTable('SysObject2Node');
    $rs =& $table->select(array('oid' => $id));

    if(!$row = $rs->getRow())
      return null;

    return $this->getPathToNode((integer)$row['node_id']);
  }

  function getPathToNode($node_id)
  {
    $toolkit =& Limb :: toolkit();
    $cache =& $toolkit->getCache();

    if(!$cache->assign($path, $node_id, PATH_TO_ID))
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
