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
require_once(LIMB_DIR . 'class/core/array_dataset.class.php');
require_once(LIMB_DIR . 'class/datasources/datasource_factory.class.php');

class datasource_component extends component 
{
	protected $parameters = array();
	protected $datasource = null;
	
	protected $total_count = 0;
	
	public function get_dataset()
	{
		$datasource = $this->_get_datasource();
		
		if (!$datasource instanceof datasource)
		{
			 debug :: write_error('data source not created',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'datasource_path' => $this->parameters['datasource_path']
    		)
    	);
			return new empty_dataset();
		}

		return $datasource->get_dataset($this->total_count, $this->_get_params_array());
	}

	public function get_total_count()
	{
		return $this->total_count;
	}
			
	public function set_parameter($name, $value)
	{
		if($name == 'order')
			$this->_set_order_parameters($value);
		elseif($name == 'limit')
			$this->_set_limit_parameters($value);
		else
			$this->parameters[$name] = $value;
	}
	
	protected function _set_limit_parameters($limit_string)
	{
		$arr = explode(',', $limit_string);
					
		$this->parameters['limit'] = isset($arr[0]) ? $arr[0] : 0;
		
		if(isset($arr[1]))
			$this->parameters['offset'] = $arr[1];
	}
	
	protected function _set_order_parameters($order_string)
	{
		$order_items = explode(',', $order_string);
		$order_pairs = array();
		foreach($order_items as $order_pair)
		{
			$arr = explode('=', $order_pair);
			
			if(!isset($arr[1]))
			{
			  $order_pairs[$arr[0]] = 'ASC';
			  continue;
			}

		  if(strtolower($arr[1]) == 'asc' || strtolower($arr[1]) == 'desc'
		  	 || strtolower($arr[1]) == 'rand()')			  
		    $order_pairs[$arr[0]] = strtoupper($arr[1]);
		  else
			  $order_pairs[$arr[0]] = 'ASC';
		}	
		
		if(sizeof($order_pairs))
			$this->parameters['order'] = $order_pairs;
	}
	
	protected function _get_datasource()
	{
		if ($this->datasource)
			return $this->datasource;
		
		$this->datasource = datasource_factory :: create($this->parameters['datasource_path']);
		
		return $this->datasource;
	}
	
	protected function _get_params_array()
	{
		return $this->parameters;
	}
}
?>