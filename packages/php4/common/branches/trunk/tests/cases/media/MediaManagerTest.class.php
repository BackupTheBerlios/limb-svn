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
require_once(dirname(__FILE__) . '/../../../MediaManager.class.php');

class MediaManagerTest extends LimbTestCase
{
  var $manager;

  function setUp()
  {
    $this->_cleanUp();

    $this->manager = new MediaManager();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    Fs :: rm(MEDIA_DIR);
  }

  function testGetMediaFilePath()
  {
    $id = 'test';
    $this->assertEqual($this->manager->getMediaFilePath($id),
                       MEDIA_DIR . $id . '.media');
  }
}

?>