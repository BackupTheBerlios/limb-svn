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
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . 'class/core/dataspace.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'class/core/permissions/user.class.php');

class content_object extends site_object
{
	protected  $_db_table = null;
		
	protected function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'can_be_parent' => 1,
			'icon' => '/shared/images/generic.gif',
			'controller_class_name' => 'empty_controller'
		);
	}
	
	protected function _define_attributes_definition()
	{
		$table = $this->get_db_table();
		
		$columns = $table->get_columns();
		
		if($key = $table->get_primary_key_name())
		  unset($columns[$key]);
			
		return $columns;
	}
		
	public function get_db_table()
	{
		if(!$this->_db_table)
		{
			$db_table_name = $this->_get_db_table_name();
				
			$this->_db_table = Limb :: toolkit()->createDBTable($db_table_name);
		}	
			
		return $this->_db_table;
	}
	
	protected function _get_db_table_name()
	{
		return 
		isset($this->_class_properties['db_table_name']) ? 
		$this->_class_properties['db_table_name'] : 
		get_class($this);
	}
	
	public function fetch($params=array(), $sql_params=array())
	{
	  $db_table = $this->get_db_table();
	  
	  $sql_params['columns'][] = ' ' . $db_table->get_columns_for_select('tn', array('id')) . ', tn.id as record_id, ';
		
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		return parent :: fetch($params, $sql_params);
	}
	
	public function recover_version($version)
	{
		if(!$version_data = $this->fetch_version($version))
			throw new LimbException('version record not found', 
			  array(
			    'class_name' => get_class($this),
			    'id' => $this->get_id(),
			    'node_id' => $this->get_node_id(),
			    'version' => $version,
			  )
			);
			
		$this->merge($version_data);
		
		$this->update();
	}

	public function fetch_version($version, $sql_params=array())
	{
		$db_table = $this->get_db_table();
		$table_name = $db_table->get_table_name();
		$id = $this->get_id();
		
		$sql = 
			sprintf( "SELECT
								%s
								ssot.id as node_id, 
								ssot.parent_id as parent_node_id, 
								ssot.level as level," . 
								$db_table->get_columns_for_select('tn', array('id', 'object_id')). ",
								tn.id as record_id,
								tn.object_id as id
								FROM 
								sys_site_object_tree as ssot,
								{$table_name} as tn
								%s
								WHERE 
								ssot.object_id = {$id} AND 
								tn.object_id = {$id} AND
								tn.version = {$version}
								%s",
								$this->_add_sql($sql_params, 'columns'),
								$this->_add_sql($sql_params, 'tables'),
								$this->_add_sql($sql_params, 'conditions')
							);
		
		$db = db_factory :: instance();
		$db->sql_exec($sql);
		
		return $db->fetch_row();
	}
	
	public function fetch_ids($params=array(), $sql_params=array(), $sort_ids = array())
	{
		$db_table = $this->get_db_table();
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		return parent :: fetch_ids($params, $sql_params, $sort_ids);
	}

	public function fetch_count($params=array(), $sql_params=array())
	{
		$db_table = $this->get_db_table();
		$table_name = $db_table->get_table_name();
		$sql_params['tables'][] = ",{$table_name} as tn";
		
		$sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
		
		return parent :: fetch_count($params, $sql_params);
	}
		
	protected function _create_version_record()
	{
		$version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');
		
		$time = time();
		
    $data['id'] = null;
		$data['object_id'] = $this->get_id();
		$data['version'] = $this->get_version();
		$data['created_date'] = $time;
		$data['modified_date'] = $time;
		$data['creator_id'] = Limb :: toolkit()->getUser()->get_id();
		
		$version_db_table->insert($data);
	}
			
	protected function _create_versioned_content_record()
	{
		$data = $this->_attributes->export();
		
    $data['id'] = null;
		$data['object_id'] = $this->get_id();
				
		$db_table = $this->get_db_table();
		$db_table->insert($data);
		
		$record_id = $db_table->get_last_insert_id();
		$this->set('record_id', $record_id);
	}
	
	protected function _update_versioned_content_record()
	{
		$data['version'] = $this->get_version();
		$data['object_id'] = $this->get_id();

		$db_table = $this->get_db_table();
		
		$row = current($db_table->get_list($data));
		
		if($row === false)
			throw new LimbException('content record not found', 
			        array(
			          'version' => $data['version'],
			          'object_id' => $data['object_id'],
			          'class_name' => get_class($this)));
		
		$id = $row['id'];

		$data = $this->_attributes->export();
	  unset($data['id']);

		$db_table->update_by_id($id, $data);
	}
	
	public function update($force_create_new_version = true)
	{
		parent :: update($force_create_new_version);
		
		if ($force_create_new_version)
		{
			$this->_create_version_record();
		
			$this->_create_versioned_content_record();
		}
		
		$this->_update_versioned_content_record();
	}
	
	public function create($is_root = false)
	{
		$id = parent :: create($is_root);
			
		$this->_create_version_record();
		
		$this->_create_versioned_content_record();
		
		return $id;
	}
				
	public function delete()
	{
		parent :: delete();

		$this->_delete_versioned_content_records();
	}

	protected function _delete_versioned_content_records()
	{
		$db_table = $this->get_db_table();	
		$db_table->delete(array('object_id' => $this->get_id()));
	}	
}

?>