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
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');

class sys_param
{
  static protected $_instance = null;
  
	protected $_db_table = null;
	protected $_types = array("char", "int", "blob", "float");
	
	function sys_param()
	{
		$this->_db_table = Limb :: toolkit()->createDBTable('sys_param');
	}
	
	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new sys_param();

    return self :: $_instance;	
	}	
	
	public function save_param($identifier, $type, $value, $force_new = true)
	{
		if(!in_array($type, $this->_types))
		{
		  throw new LimbException('trying to save undefined type in sys_param', 
			  array('type' => $type, 'param' => $identifier));
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
					'type' => $type,
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
					"id" => null,
					'identifier' => $identifier,
					'type' => $type,
					"{$type}_value" => $value,
			);
			
			$this->_db_table->insert($data);
			
			return $this->_db_table->get_last_insert_id();
		}
	}
	
	public function get_param($identifier, $type='')
	{
		if(!empty($type) && !in_array($type, $this->_types))
		{
		  throw new LimbException('trying to get undefined type in sys_param', 
			  array('type' => $type, 'param' => $identifier));
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