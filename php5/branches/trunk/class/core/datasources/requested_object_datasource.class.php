<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/site_objects_datasource.class.php');

class requested_object_datasource extends site_objects_datasource 
{
  protected $object_id;
  protected $node_mapped_by_request;
  protected $request;
  
  public function reset()
  {
    parent :: reset();
    
    $this->object_id = null;
    $this->node_mapped_by_request = null;
    $this->request = null;
  }
  
  public function set_request($request)
  {
    $this->request = $request;
  }
  
  public function get_object_ids()
  {
    if($this->object_id)
      return array($this->object_id);
    
    if($this->request && $node = $this->map_request_to_node($this->request))
    {
      $this->object_id = $node['object_id'];
      return array($this->object_id);
    }
    else
      throw new LimbException('request is null');
  }

	public function map_uri_to_node($uri, $recursive = false)
	{
		$tree = Limb :: toolkit()->getTree();

		if(($node_id = $uri->get_query_item('node_id')) === false)
			$node = $tree->get_node_by_path($uri->get_path(), '/', $recursive);
		else
			$node = $tree->get_node((int)$node_id);

		return $node;
	}

	public function map_request_to_node($request)
	{
		if($this->node_mapped_by_request)
			return $this->node_mapped_by_request;

		if($node_id = $request->get('node_id'))
			$node = Limb :: toolkit()->getTree()->get_node((int)$node_id);
		else
		  $node = $this->map_uri_to_node($request->get_uri());

		$this->node_mapped_by_request = $node;
		return $node;
	}
}

?> 