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

    if(!$path = $tree->getPathToNode($row['node_id']))
       return null;

    return $this->_applyResultingPathOffsets($path);
  }

  function _applyResultingPathOffsets($path)
  {
    $path = str_replace($this->int_offset, '', $path);
    return $this->ext_offset . $path;
  }
}

?>
