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
require_once(LIMB_DIR . '/class/lib/image/ImageLibrary.class.php');
require_once(dirname(__FILE__) . '/../../../ImageVariation.class.php');
require_once(dirname(__FILE__) . '/../../../MediaManager.class.php');

Mock :: generatePartial('ImageVariation',
                 'ImageVariationTestVersion1',
                 array('_generateTempFile',
                       '_getImageLibrary',
                       '_getMediaManager'));

Mock :: generatePartial('ImageVariation',
                 'ImageVariationTestVersion2',
                 array('_generateTempFile',
                       '_getImageLibrary',
                       '_getMediaManager',
                       '_updateDimensionsUsingFile',
                       '_unlinkTempFile'));

Mock :: generate('ImageLibrary');
Mock :: generate('MediaManager');

class ImageVariationTest extends LimbTestCase
{
  var $variation;
  var $image_library;
  var $media_manager;

  function setUp()
  {
    $this->image_library = new MockImageLibrary($this);
    $this->media_manager = new MockMediaManager($this);

    $this->variation = new ImageVariationTestVersion1($this);
    $this->variation->__construct();
    $this->variation->setReturnValue('_getImageLibrary', $this->image_library);
    $this->variation->setReturnValue('_getMediaManager', $this->media_manager);
  }

  function tearDown()
  {
    $this->variation->tally();
    $this->image_library->tally();
    $this->media_manager->tally();
  }

  function testLoadFromFile()
  {
    $this->media_manager->expectOnce('store', array($file = dirname(__FILE__) . '/1.jpg'));
    $this->media_manager->setReturnValue('store', $media_file_id = 'sd3232cvc1op', array($file));

    $this->variation->loadFromFile($file);

    $this->assertEqual($this->variation->getWidth(), 100);
    $this->assertEqual($this->variation->getHeight(), 137);
    $this->assertEqual($this->variation->getMediaFileId(), $media_file_id);
  }

  function testResize()
  {
    $this->variation = new ImageVariationTestVersion2($this);
    $this->variation->__construct();
    $this->variation->setReturnValue('_getImageLibrary', $this->image_library);
    $this->variation->setReturnValue('_getMediaManager', $this->media_manager);

    $this->variation->setMimeType($mime_type = 'jpeg');
    $this->variation->setMediaFileId($media_file_id = 'sd3232cvc1op');//remember explicit calls are forbidden!!!

    $this->media_manager->setReturnValue('getMediaFilePath', $media_file_path = 'mediaPath', array($media_file_id));

    $this->variation->expectOnce('_generateTempFile');
    $this->variation->setReturnValue('_generateTempFile', $output_temp_file = 'test');

    $this->image_library->expectOnce('getImageType', array($mime_type));
    $this->image_library->setReturnValue('getImageType', $input_file_type = 'jpeg', array($mime_type));

    $this->image_library->expectOnce('fallBackToAnySupportedType', array($input_file_type));
    $this->image_library->setReturnValue('fallBackToAnySupportedType', $output_file_type = 'png', array($input_file_type));

    $this->image_library->expectOnce('setInputFile', array($media_file_path));
    $this->image_library->expectOnce('setInputType', array($input_file_type));

    $this->image_library->expectOnce('setOutputFile', array($output_temp_file));
    $this->image_library->expectOnce('setOutputType', array($output_file_type));
    $this->image_library->expectOnce('resize', array(array('maxDimension' => $max_size = 30)));
    $this->image_library->expectOnce('commit');

    $this->media_manager->expectOnce('store', array($output_temp_file));
    $this->media_manager->setReturnValue('store', $new_media_file_id = 'fsdfsd7878sda', array($output_temp_file));

    $this->variation->expectOnce('_updateDimensionsUsingFile', array($output_temp_file));
    $this->variation->expectOnce('_unlinkTempFile', array($output_temp_file));

    $this->variation->resize($max_size);

    $this->assertEqual($new_media_file_id, $this->variation->getMediaFileId());
  }

}

?>