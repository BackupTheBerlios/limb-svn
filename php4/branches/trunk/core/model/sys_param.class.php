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

require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');

class sys_param
{
	var $_db_table = null;
	var $_types = array("char", "int", "blob", "float");
	
	function sys_param()
	{
		$this->_db_table =& db_table_factory :: instance('sys_param');
	}
	
	function & instance($force = false)
	{
	  $obj = null;
  	$object_name = 'global_sys_param_object';
  	if(isset($GLOBALS[$object_name]))
			$obj =& $GLOBALS[$object_name];
		
  	if(!$obj || get_class($obj) != 'sys_param' || $force)
  	{
  		$obj = & new sys_param();
  		$GLOBALS[$object_name] =& $obj;
  	}
  	
  	return $obj;
	}
	

	function save_param($identifier, $type, $value, $force_new = true)
	{
		if(!in_array($type, $this->_types))
		{
		  debug :: write_error('trying to save undefined type in sys_param', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('type' => $type, 'param' => $identifier));
			return false;
		}

		$params = $this->_db_table->get_list("identifier='{$identifier}'", '', '', 0, 1);
		
		if(empty($value))//?
		{ 
			if ($type == 'int' || $type == 'float')
				$value = (int) $value;
			else
				$value = (string) $value;
		}
		
		if(is_array($params) && count($params))
		{
			$param = current($params);
			
			$data = array(
					"type" => $type,
					"{$type}_value" => $value,
			);

			if($force_new)
			{
				foreach($this->_types as $type_name)
					if($type_name != $type)
							$data["{$type_name}_value"] =  NULL;
						
			}
			return $this->_db_table->update_by_id($param['id'], $data);

		}
		else
		{
			$data = array(
					'identifier' => $identifier,
					'type' => $type,
					"{$type}_value" => $value,
			);
			
			$this->_db_table->insert($data);
			
			return $this->_db_table->get_last_insert_id();
		}
	}
	
	
	function get_param($identifier, $type='')
	{
		if(!empty($type) && !in_array($type, $this->_types))
		{
		  debug :: write_error('trying to get undefined type in sys_param', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			  array('type' => $type, 'param' => $identifier));
			return null;
		}

		$params = $this->_db_table->get_list("identifier='{$identifier}'", '', '', 0, 1);

		if(!is_array($params) || !count($params))
			return null;

		$param = current($params);

		if (empty($type))
			$type = $param['type'];

		return $param["{$type}_value"];
	}	
	
	
}
?>