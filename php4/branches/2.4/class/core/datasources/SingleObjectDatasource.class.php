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
require_once(dirname(__FILE__) . '/SiteObjectsDatasource.class.php');

class SingleObjectDatasource extends SiteObjectsDatasource
{
  protected $path;
  protected $node_id;
  protected $object_id;

  function __construct()
  {
    $this->reset();
  }

  public function setPath($path)
  {
    $this->path = $path;
  }

  public function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  public function setObjectId($object_id)
  {
    $this->object_id = $object_id;
  }

  public function reset()
  {
    parent :: reset();

    $this->path = '';
    $this->node_id = null;
    $this->object_id = null;
  }

  public function getObjectIds()
  {
    if ($this->object_id)
      return array($this->object_id);

    if ($this->node_id &&  $object_id = $this->_getObjectIdByNodeId())
      return array($object_id);

    if ($this->path &&  $object_id = $this->_getObjectIdByPath())
      return array($object_id);

    return array();
  }

  protected function _getObjectIdByNodeId()
  {
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->getNode($this->node_id);
    if (!$node)
      return null;
    else
      return $node['object_id'];
  }

  protected function _getObjectIdByPath()
  {
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->getNodeByPath($this->path);
    if (!$node)
      return null;
    else
      return $node['object_id'];
  }
}

?>