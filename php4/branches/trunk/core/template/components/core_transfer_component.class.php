<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: support@limb-project.com 
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: outputcache_component.class.php 816 2004-10-21 13:04:53Z pachanga $
*
***********************************************************************************/ 
class core_transfer_component extends component
{
	function make_transfer($hash_id, $target_name)
	{ 
    $data = $this->parent->get($hash_id);
      
    if ($target_component =& $this->parent->find_child($target_name))
    {
      if(method_exists($target_component, 'register_dataset'))
        $target_component->register_dataset(new array_dataset($data));
    }
    elseif($target_component =& $this->root->find_child($target_name))
    {
      if(method_exists($target_component, 'register_dataset'))
        $target_component->register_dataset(new array_dataset($data));
    }
	}
} 

?>