<?php

require_once(LIMB_DIR . 'core/lib/util/array_dataset.class.php');
require_once(LIMB_DIR . 'core/data_source/data_source_factory.class.php');

class data_source_component extends component 
{
	var $parameters = array();
	var $data_source = null;
	
	var $total_count = 0;
	
	function & get_dataset()
	{
		$data_source =& $this->_get_data_source();
		if (!is_a($data_source, 'data_source'))
		{
			 debug :: write_error('data source not created',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'data_source_path' => $this->parameters['data_source_path']
    		)
    	);
			return new empty_dataset();
		}

		$dataset =& $data_source->get_data_set($this->total_count, $this->_get_params_array());
		
		return $dataset;
	}

	function get_total_count()
	{
		return $this->total_count;
	}
			
	function set_parameter($name, $value)
	{
		if($name == 'order')
			$this->_set_order_parameters($value);
		elseif($name == 'limit')
			$this->_set_limit_parameters($value);
		else
			$this->parameters[$name] = $value;
	}
	
	function _set_limit_parameters($limit_string)
	{
		$arr = explode(',', $limit_string);
					
		$this->parameters['limit'] = isset($arr[0]) ? $arr[0] : 0;
		
		if(isset($arr[1]))
			$this->parameters['offset'] = $arr[1];
	}
	
	function _set_order_parameters($order_string)
	{
		$order_items = explode(',', $order_string);
		$order_pairs = array();
		foreach($order_items as $order_pair)
		{
			list($field, $order_type) = explode('=', $order_pair);
			$order_pairs[$field] = $order_type;
		}	
		
		$this->parameters['order'] = $order_pairs;
	}
	
	function & _get_data_source()
	{
		if ($this->data_source)
			return $this->data_source;
		
		$this->data_source =& data_source_factory :: create($this->parameters['data_source_path']);
		
		return $this->data_source;
	}
	
	function _get_params_array()
	{
		return $this->parameters;
	}
}
?>