<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/template/components/form/input_form_element.class.php');

class node_select_component extends input_form_element
{
	public function init_node_select()
	{
		if (defined('NODE_SELECT_LOAD_SCRIPT'))
			return;
					
		echo "<script type='text/javascript' src='/shared/js/node_select.js'></script>";
				
		define('NODE_SELECT_LOAD_SCRIPT', 1);		
	}
	
	public function render_node_select()
	{ 
		$id = $this->get_attribute('id');
  	$md5id = substr(md5($id), 0, 5);

  	if($node_id = $this->get_value())
	  	$object_data = fetch_one_by_node_id($node_id);
	  else
	    $object_data = false;
  	
  	if($object_data !== false)
  	{
			$identifier = $object_data['identifier'];
			$title = $object_data['title'];
			$path = $object_data['path'];
			$class_name = $object_data['class_name'];
			$icon = $object_data['icon'];
			$parent_node_id = $object_data['parent_node_id'];

		}
		else
		{
			$identifier = '';
			$title = '';
			$path = '';
			$class_name = '';
			$parent_node_id = '';
			$icon = '/shared/images/no.gif';
		}
		
  	echo "	<img id='{$md5id}_icon' align='center' src='{$icon}'/>&nbsp;
	 					<b><span id='{$md5id}_path'>{$path}</span></b>
  					<span style='display:none;'>
    				<span id='{$md5id}_node_id'>{$node_id}</span>
    				<span id='{$md5id}_parent_node_id'>{$parent_node_id}</span>
    				<span id='{$md5id}_identifier'>{$identifier}</span>
  					<span id='{$md5id}_title'>{$title}</span>
  					<span id='{$md5id}_class_name'>{$class_name}</span>
  					</span>";
  	
  	$start_path_condition = "";
  	$only_parents_condition = "";
  	$start_path = $this->get_attribute('start_path');
  	if(!$start_path)
  		$start_path = session :: get('limb_node_select_working_path');
  	if(!$start_path)
			$start_path = '/root';
  	
  	$start_path_condition = "node_select_{$md5id}.set_start_path('{$start_path}');";

  	if($only_parents = $this->get_attribute('only_parents'))
  		$only_parents_condition = "node_select_{$md5id}.set_only_parents_restriction('{$only_parents}');";

  	echo "<script type='text/javascript'>
		    	var node_select_{$md5id};
		    	
		      function init_node_select_{$md5id}()
		      {
		        node_select_{$md5id} = new node_select('{$id}', '{$md5id}');
		        {$start_path_condition}
		        {$only_parents_condition}
		        node_select_{$md5id}.generate();
		      }
		      
		      function node_select_{$md5id}_insert_node(node)
		      {
		      	node_select_{$md5id}.insert_node(node);
		      }
	
		      function node_select_{$md5id}_get_node()
		      {
		      	return node_select_{$md5id}.get_node();
		      }
		      		     
		      function node_reset_{$md5id}()
		      {
		      	node_select_{$md5id}.reset();
		      }

		      add_event(window, 'load', init_node_select_{$md5id});
		    </script>";
	    
	  echo "<input class='button' type='button' onclick='popup(\"/root/node_select\", null, null, false, node_select_{$md5id}_insert_node, node_select_{$md5id}_get_node)' value=' ... '>";

		if($this->get_attribute('reset_button'))
		{
		  echo '&nbsp;';
		  echo "<input class='button' type='button' onclick='node_reset_{$md5id}()' value='" . strings :: get('reset'). "'>";
		}
	}
} 
?>