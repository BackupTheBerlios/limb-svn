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
class datasource_tag_info
{
	public $tag = 'DATASOURCE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'datasource_tag';
} 

register_tag(new datasource_tag_info());

class datasource_tag extends server_component_tag
{
  function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/datasource_component';
	}
	
	public function pre_parse()
	{
		if (!isset($this->attributes['target']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'target',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

    $this->_check_order_parameter();

    return PARSER_REQUIRE_PARSING;
	}
	
	protected function _check_order_parameter()
	{
	  if (!isset($this->attributes['order']))
	    return;
	  
		$order_items = explode(',', $this->attributes['order']);
		$order_pairs = array();
		foreach($order_items as $order_pair)
		{
			$arr = explode('=', $order_pair);
			
			if(!isset($arr[1]))
			  continue;

		  if(strtolower($arr[1]) != 'asc' && strtolower($arr[1]) != 'desc'
		  	 && !strtolower($arr[1]) == 'rand()')			  
  			throw new WactException('wrong order type', 
					array('tag' => $this->tag,
					'order_value' => $arr[1],
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
	  }		
	}
		
	public function generate_contents($code)
	{
		parent :: generate_contents($code);
		
		$navigator = null;
		
		if(isset($this->attributes['navigator']))
		{
			if($navigator = $this->parent->find_child($this->attributes['navigator']))
			{
				$limit = $code->get_temp_variable();
				$offset = $code->get_temp_variable();
				
				$code->write_php('$' . $limit . '= ' . $navigator->get_component_ref_code() . '->get_items_per_page();');
				$code->write_php($this->get_component_ref_code() . '->set_parameter("limit", $' . $limit . ');');

				$code->write_php('if(isset($_GET["page_' . $navigator->get_server_id() . '"])){');
        $code->write_php('$' . $offset . '= ($_GET["page_' . $navigator->get_server_id() . '"]-1)*$' . $limit . ';');
        $code->write_php($this->get_component_ref_code() . '->set_parameter("offset", $' . $offset . ');');
				$code->write_php('}');
			}			
		}

		$targets = explode(',', $this->attributes['target']);
		foreach($targets as $target)
		{
		  $target = trim($target);
		  
		  $target_component = $this->parent->find_child($target);
		  
		  if(!$target_component)
		  {
		    $root = $this->get_root_dataspace();
		    $target_component = $root->find_child($target);
		  }
		    
			if($target_component)
			{
				$code->write_php($target_component->get_component_ref_code() . '->register_dataset(' . $this->get_component_ref_code() . '->get_dataset());');
				
				if($navigator)
				{
				  $code->write_php('if(isset($' . $offset. ')){');
				  $code->write_php($target_component->get_component_ref_code() . '->set_offset($' . $offset . ');');
				  $code->write_php('}');
				}
			}
			else
  			throw new WactException('target component not found', 
					array('target' => $target));
		}
			
		if(isset($this->attributes['navigator']) && $navigator)
		{
			$code->write_php($navigator->get_component_ref_code() . '->set_total_items(' . $this->get_component_ref_code() . '->get_total_count());');
		}
	} 	
} 

?>