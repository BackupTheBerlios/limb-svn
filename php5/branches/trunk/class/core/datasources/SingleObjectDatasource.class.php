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
require_once(dirname(__FILE__) . '/site_objects_datasource.class.php');

class single_object_datasource extends site_objects_datasource
{
  protected $path;
  protected $node_id;
  protected $object_id;

  function __construct()
  {
    $this->reset();
  }

  public function set_path($path)
  {
    $this->path = $path;
  }

  public function set_node_id($node_id)
  {
    $this->node_id = $node_id;
  }

  public function set_object_id($object_id)
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

  public function get_object_ids()
  {
    if ($this->object_id)
      return array($this->object_id);

    if ($this->node_id && $object_id = $this->_get_object_id_by_node_id())
      return array($object_id);

    if ($this->path && $object_id = $this->_get_object_id_by_path())
      return array($object_id);

    return array();
  }

  protected function _get_object_id_by_node_id()
  {
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->get_node($this->node_id);
    if (!$node)
      return null;
    else
      return $node['object_id'];
  }

  protected function _get_object_id_by_path()
  {
    $tree = Limb :: toolkit()->getTree();
    $node = $tree->get_node_by_path($this->path);
    if (!$node)
      return null;
    else
      return $node['object_id'];
  }
}

?>