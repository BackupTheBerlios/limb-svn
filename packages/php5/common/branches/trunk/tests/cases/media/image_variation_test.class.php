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
require_once(LIMB_DIR . '/class/lib/image/image_library.class.php');
require_once(dirname(__FILE__) . '/../../../image_variation.class.php');
require_once(dirname(__FILE__) . '/../../../media_manager.class.php');

Mock :: generatePartial('image_variation',
                 'image_variation_test_version1',
                 array('_generate_temp_file',
                       '_get_image_library',
                       '_get_media_manager'));

Mock :: generatePartial('image_variation',
                 'image_variation_test_version2',
                 array('_generate_temp_file',
                       '_get_image_library',
                       '_get_media_manager',
                       '_update_dimensions_using_file',
                       '_unlink_temp_file'));

Mock :: generate('image_library');
Mock :: generate('media_manager');

class image_variation_test extends LimbTestCase
{
  var $variation;
  var $image_library;
  var $media_manager;

  function setUp()
  {
    $this->image_library = new Mockimage_library($this);
    $this->media_manager = new Mockmedia_manager($this);

    $this->variation = new image_variation_test_version1($this);
    $this->variation->__construct();
    $this->variation->setReturnValue('_get_image_library', $this->image_library);
    $this->variation->setReturnValue('_get_media_manager', $this->media_manager);
  }

  function tearDown()
  {
    $this->variation->tally();
    $this->image_library->tally();
    $this->media_manager->tally();
  }

  function test_load_from_file()
  {
    $this->media_manager->expectOnce('store', array($file = dirname(__FILE__) . '/1.jpg'));
    $this->media_manager->setReturnValue('store', $media_file_id = 'sd3232cvc1op', array($file));

    $this->variation->load_from_file($file);

    $this->assertEqual($this->variation->get_width(), 100);
    $this->assertEqual($this->variation->get_height(), 137);
    $this->assertEqual($this->variation->get_media_file_id(), $media_file_id);
  }

  function test_resize()
  {
    $this->variation = new image_variation_test_version2($this);
    $this->variation->__construct();
    $this->variation->setReturnValue('_get_image_library', $this->image_library);
    $this->variation->setReturnValue('_get_media_manager', $this->media_manager);

    $this->variation->set_mime_type($mime_type = 'jpeg');
    $this->variation->set_media_file_id($media_file_id = 'sd3232cvc1op');//remember explicit calls are forbidden!!!

    $this->media_manager->setReturnValue('get_media_file_path', $media_file_path = 'media_path', array($media_file_id));

    $this->variation->expectOnce('_generate_temp_file');
    $this->variation->setReturnValue('_generate_temp_file', $output_temp_file = 'test');

    $this->image_library->expectOnce('get_image_type', array($mime_type));
    $this->image_library->setReturnValue('get_image_type', $input_file_type = 'jpeg', array($mime_type));

    $this->image_library->expectOnce('fall_back_to_any_supported_type', array($input_file_type));
    $this->image_library->setReturnValue('fall_back_to_any_supported_type', $output_file_type = 'png', array($input_file_type));

    $this->image_library->expectOnce('set_input_file', array($media_file_path));
    $this->image_library->expectOnce('set_input_type', array($input_file_type));

    $this->image_library->expectOnce('set_output_file', array($output_temp_file));
    $this->image_library->expectOnce('set_output_type', array($output_file_type));
    $this->image_library->expectOnce('resize', array(array('max_dimension' => $max_size = 30)));
    $this->image_library->expectOnce('commit');

    $this->media_manager->expectOnce('store', array($output_temp_file));
    $this->media_manager->setReturnValue('store', $new_media_file_id = 'fsdfsd7878sda', array($output_temp_file));

    $this->variation->expectOnce('_update_dimensions_using_file', array($output_temp_file));
    $this->variation->expectOnce('_unlink_temp_file', array($output_temp_file));

    $this->variation->resize($max_size);

    $this->assertEqual($new_media_file_id, $this->variation->get_media_file_id());
  }

}

?>