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
require_once(LIMB_DIR . '/class/core/site_objects/SiteObjectFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(dirname(__FILE__) . '/../../MetadataManager.class.php');

class SaveMetadataTest extends LimbTestCase
{
  var $db = null;

  function setUp()
  {
    $this->db = DbFactory :: instance();
    $this->db->sqlDelete('sys_metadata');
  }

  function tearDown()
  {
    $this->db->sqlDelete('sys_metadata');
  }

  function testSave()
  {
    $result_id = MetadataManager :: saveMetadata(1, 'keywords', 'description');

    $this->assertNotNull($result_id);

    $sys_metadata_db_table = DbTableFactory :: create('SysMetadata');
    $metadata_row = $sys_metadata_db_table->getRowById($result_id);

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