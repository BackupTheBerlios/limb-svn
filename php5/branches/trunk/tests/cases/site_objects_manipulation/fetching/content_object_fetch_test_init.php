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
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/db_tables/content_object_db_table.class.php');
require_once(dirname(__FILE__) . '/site_object_fetch_test_init.php');

class content_object_fetch_test_init extends site_object_fetch_test_init
{
  function init(& $object)
  {
  	parent :: init($object);

  	$this->_insert_content_object_records($object);
  }

  function _insert_content_object_records($object)
  {
  	$db_table =& $object->get_db_table();

  	$sys_db_table =& db_table_factory :: create('sys_site_object');

  	$data = array();
  	$version_data = array();
  	for($i = 1; $i <= 5; $i++)
  	{
  		$record = $sys_db_table->get_row_by_id($i);

  		for($k = $record['current_version']; $k <= $record['current_version']; $k++)
  		{
	  		$data['id'] = null;
	  		$data['version'] = $k;
	  		$data['object_id'] = $i;
	  		$data['annotation'] = 'object_' . $i . '_annotation_version_' . $k;
	  		$data['news_date'] = '2003-01-02';
	  		$data['content'] = 'object_' . $i . '_content_version_' . $k;
	  		$data['identifier'] = 'object_' . $i;
	  		$data['title'] = 'object_' . $i . '_title';

	  		$db_table->insert($data);
  		}
  	}
  }

  function _insert_object_version_records($object_id, $version_max)
  {
  	$version_db_table =& db_table_factory :: create('sys_object_version');

  	for($i = 1; $i <= $version_max; $i++)
  	{
  		$data = array();
  		$data['id'] = null;
  		$data['version'] = $i;
  		$data['object_id'] = $object_id;

  		$version_db_table->insert($data);
		}
  }

}

?>