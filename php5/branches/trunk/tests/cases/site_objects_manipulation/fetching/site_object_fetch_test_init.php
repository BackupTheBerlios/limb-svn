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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');

class site_object_fetch_test_init
{
	var $db = null;
	var $class_id = '';
	var $root_node_id = '';
  var $controller_id = '';

  function site_object_fetch_test_init()
  {
		$this->db = db_factory :: instance();
  }

  function init(& $object)
  {
  	$this->class_id = $object->get_class_id();

    $behaviour = new site_object_behaviour();
    $this->behaviour_id = $behaviour->get_id();

  	$this->_insert_sys_site_object_records();
  	$this->_insert_fake_sys_site_object_records();
  }

  function _insert_sys_site_object_records()
  {
  	$db_table =& db_table_factory :: create('sys_site_object');

  	$tree = new tree();
		$values['identifier'] = 'root';
		$this->root_node_id = $tree->create_root_node($values, false, true);

  	$data = array();
  	for($i = 1; $i <= 5; $i++)
  	{
  		$version = mt_rand(1, 3);
  		$this->_insert_object_version_records($i, $version);

  		$this->db->sql_insert('sys_site_object',
  			array(
  				'id' => $i,
  				'class_id' => $this->class_id,
  				'behaviour_id' => $this->behaviour_id,
  				'current_version' => $version,
  				'identifier' => 'object_' . $i,
  				'title' => 'object_' . $i . '_title',
  				'status' => 0,
  				'locale_id' => 'en',
  			)
  		);

			$values['identifier'] = 'object_' . $i;
			$values['object_id'] = $i;
			$tree->create_sub_node($this->root_node_id, $values);
  	}
  }

  function _insert_object_version_records($object_id, $version_max)
  {
	}

  function _insert_fake_sys_site_object_records()
  {
  	$class_db_table = db_table_factory :: create('sys_class');
  	$class_db_table->insert(array('id' => 1001, 'class_name' => 'fake_class'));

  	$db_table =& db_table_factory :: create('sys_site_object');

  	$tree = new tree();

  	$data = array();
  	for($i = 6; $i <= 10 ; $i++)
  	{
  		$this->db->sql_insert('sys_site_object',
  			array(
  				'id' => $i,
  				'class_id' => 1001,
  				'behaviour_id' => $this->behaviour_id,
  				'identifier' => 'object_' . $i,
  				'title' => 'object_' . $i . '_title',
  				'status' => 0,
  				'locale_id' => 'en',
  			)
  		);

			$values['identifier'] = 'object_' . $i;
			$values['object_id'] = $i;
			$tree->create_sub_node($this->root_node_id, $values);
  	}
  }
}

?>