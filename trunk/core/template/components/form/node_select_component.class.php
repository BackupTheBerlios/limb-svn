<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: image_select_component.class.php 46 2004-03-19 12:45:55Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/template/components/form/input_form_element.class.php');

class node_select_component extends input_form_element
{
	function init_node_select()
	{
		if (defined('NODE_SELECT_LOAD_SCRIPT'))
			return;
					
		echo "<script type='text/javascript' src='/shared/js/node_select.js'></script>";
		
		if (!defined('RICHEDIT_POPURL_SCRIPT'))
    	echo "<script type='text/javascript' src='/shared/richedit/popupurl.js'></script>";
			
		define('NODE_SELECT_LOAD_SCRIPT', 1);
		define('RICHEDIT_POPURL_SCRIPT', 1);
		
		if(!$node_id = $this->get_value())
		{
			$object_data = fetch_mapped_by_url();
			$this->set_value($object_data['node_id']);
		}
	}
	
	function render_node_select()
	{ 
		$id = $this->get_attribute('id');
  	$md5id = substr(md5($id), 0, 5);

  	$node_id = $this->get_value();
  	$object_data = fetch_one_by_node_id($node_id);
		
		$identifier = $object_data['identifier'];
		$title = $object_data['title'];
		$path = $object_data['path'];
		$class_name = $object_data['class_name'];
		$icon = $object_data['icon'];
		$parent_node_id = $object_data['parent_node_id'];
  	
  	echo "<b>{$path}</b><br>";
  	  	
  	echo "	<img id='{$md5id}_icon' align='center' src='{$icon}'/>&nbsp;
	 					<span id='{$md5id}_path'>{$path}</span><br>
  					<span style='display:none;'>
    				<span id='{$md5id}_identifier'>{$identifier}</span>
    				<span id='{$md5id}_parent_node_id'>{$parent_node_id}</span>
  					<span id='{$md5id}_title'>{$title}</span>
  					<span id='{$md5id}_class_name'>{$class_name}</span>
  					</span>";
  	
  	echo "<script type='text/javascript'>
		    	var node_select_{$md5id};
		    	
		      function init_node_select_{$md5id}()
		      {
		        node_select_{$md5id} = new node_select('{$id}', '{$md5id}');
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
		      		     
		      add_event(window, 'load', init_node_select_{$md5id});
		    </script>";
	    
	  echo "<input class='button' type='button' onclick='PopupURL(null, \"/root/parent_select\", node_select_{$md5id}_insert_node, node_select_{$md5id}_get_node)' value='Select parent'>";
	}
	
} 
?>