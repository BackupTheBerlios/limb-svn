<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(dirname(__FILE__) . '/../../metadata_manager.class.php');

class save_metadata_test extends LimbTestCase
{
  var $db = null;

  function setUp()
  {
    $this->db = db_factory :: instance();
    $this->db->sql_delete('sys_metadata');
  }

  function tearDown()
  {
    $this->db->sql_delete('sys_metadata');
  }

  function test_save()
  {
    $result_id = metadata_manager :: save_metadata(1, 'keywords', 'description');

    $this->assertNotNull($result_id);

    $sys_metadata_db_table = db_table_factory :: create('sys_metadata');
    $metadata_row = $sys_metadata_db_table->get_row_by_id($result_id);

    $this->assertTrue(is_array($metadata_row));
    $this->assertTrue(isset($metadata_row['object_id']));
    $this->assertTrue(isset($metadata_row['keywords']));
    $this->assertTrue(isset($metadata_row['description']));

    $this->assertEqual($metadata_row['object_id'], 1);
    $this->assertEqual($metadata_row['keywords'], 'keywords');
    $this->assertEqual($metadata_row['description'], 'description');
  }
}
?>