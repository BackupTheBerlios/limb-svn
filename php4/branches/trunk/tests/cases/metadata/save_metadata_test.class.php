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

require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

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
    $metadata['id'] = 1;
    $metadata['keywords'] = 'keywords';
    $metadata['description'] = 'description';

    $o =& site_object_factory :: create('site_object');

    $o->merge_attributes($metadata);
    $result_id = $o->save_metadata();

    $this->assertNotNull($result_id);

    $sys_metadata_db_table =& db_table_factory :: instance('sys_metadata');
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