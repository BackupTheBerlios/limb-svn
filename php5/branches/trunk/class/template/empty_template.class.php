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
class empty_template
{
  public function find_parent_by_class($class)
  {
    return null;
  }
  
  public function find_child_by_class($class)
  {
    return null;
  }
  
  public function find_child($server_id)
  {
    return null;
  }
  
	public function get_child($server_id)
	{
		return null;
	} 

	public function display()
	{
	  throw new LimbException('template is empty');		
	} 
} 

?>