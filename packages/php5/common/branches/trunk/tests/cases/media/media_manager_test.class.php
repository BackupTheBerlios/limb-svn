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
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

class media_manager_test extends LimbTestCase
{
  var $manager;

  function setUp()
  {
    $this->_clean_up();

    $this->manager = new media_manager();
  }

  function tearDown()
  {
    $this->_clean_up();
  }

  function _clean_up()
  {
    fs :: rm(MEDIA_DIR);
  }

  function test_get_media_file_path()
  {
    $id = 'test';
    $this->assertEqual($this->manager->get_media_file_path($id),
                       MEDIA_DIR . $id . '.media');
  }
}

?>