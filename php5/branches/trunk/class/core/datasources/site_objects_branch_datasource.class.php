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

class site_objects_branch_datasource extends site_objects_datasource
{
  protected $path;
  protected $check_expanded_parents;
  protected $include_parent;
  protected $depth;
  
  function set_path($path)
  {
    $this->path = $path;
  }

  function set_check_expanded_parents($status = true)
  {
    $this->check_expanded_parents = $status;
  }

  function set_include_parent($status = true)
  {
    $this->include_parent = $status;
  }

  function set_depth($depth)
  {
    $this->depth = $depth;
  }
  
  function reset()
  {
    parent :: reset();
    
    $this->path = '';
    $this->check_expanded_parents = false;
    $this->include_parent = false;
    $this->depth = 1;
  }
  
  public function get_object_ids()
  {
    if ($this->object_ids)
      return $this->object_ids;
    
    $tree = Limb :: toolkit()->getTree();
		if(!$nodes = $tree->get_sub_branch_by_path($this->path, 
                                               $this->depth,
                                               $this->include_parent, 
                                               $this->check_expanded_parents))
    {
			return array();
    }  

    $this->object_ids = complex_array :: get_column_values('object_id', $nodes);
    
    return $this->object_ids;
  }
}

?> 