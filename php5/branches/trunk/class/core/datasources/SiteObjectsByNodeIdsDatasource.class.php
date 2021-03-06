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

class SiteObjectsByNodeIdsDatasource extends SiteObjectsDatasource
{
  protected $node_ids;
  protected $use_node_ids_as_keys;

  function setNodeIds($node_ids)
  {
    $this->node_ids = $node_ids;
  }

  function setUseNodeIdsAsKeys($status = true)
  {
    $this->use_node_ids_as_keys = $status;
  }

  function reset()
  {
    parent :: reset();

    $this->node_ids = array();
    $this->use_node_ids_as_keys = false;
  }

  public function getObjectIds()
  {
    if ($this->object_ids)
      return $this->object_ids;

    if (!$this->node_ids)
      return array();

    $tree = Limb :: toolkit()->getTree();
    if (!$nodes = Limb :: toolkit()->getTree()->getNodesByIds($this->node_ids))
      return array();

    $this->object_ids = ComplexArray :: getColumnValues('object_id', $nodes);

    return $this->object_ids;
  }

  protected function _doParentFetch()
  {
    return parent :: fetch();
  }

  public function fetch()
  {
    $objects_data = $this->_doParentFetch();

    if (!$this->use_node_ids_as_keys)
      return $objects_data;
    else
    {
      $result = array();

      foreach($objects_data as $object_data)
        $result[$object_data['node_id']] = $object_data;

      return $result;
    }
  }
}

?>