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
require_once(dirname(__FILE__) . '/../../../FileObject.class.php');
require_once(dirname(__FILE__) . '/../../../MediaManager.class.php');

Mock :: generatePartial('FileObject',
                 'FileObjectTestVersion',
                 array('_getMediaManager'));

Mock :: generate('MediaManager');

class FileObjectTest extends LimbTestCase
{
  var $file;
  var $media_manager;

  function setUp()
  {
    $this->media_manager = new MockMediaManager($this);

    $this->file = new FileObjectTestVersion($this);
    $this->file->FileObject();
    $this->file->setReturnReference('_getMediaManager', $this->media_manager);
  }

  function tearDown()
  {
    $this->file->tally();
    $this->media_manager->tally();
  }

  function testLoadFromFile()
  {
    $this->media_manager->expectOnce('store', array($file = dirname(__FILE__) . '/1.jpg'));
    $this->media_manager->setReturnValue('store', $media_file_id = 'sd3232cvc1op', array($file));

    $this->file->loadFromFile($file);

    $this->assertEqual($this->file->getMediaFileId(), $media_file_id);
  }
}

?>