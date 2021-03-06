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
if (!defined('PHP_IMAGE_DIR_C'))
  define('PHP_IMAGE_DIR_C', LIMB_DIR . 'core/lib/image/');

SimpleTestOptions::ignore('image_library_test');

class image_library_test extends UnitTestCase 
{
  var $library = null;
  var $input_file = '';
  var $output_file = '';
    
  function setUp()
  {
  	$this->input_file = LIMB_DIR . '/tests/cases/image/images/input.jpg';
  	$this->output_file = VAR_DIR . '/output.jpg';
	
	if(!file_exists($this->output_file))
		touch($this->output_file);

    $input_type = 'jpeg';
    $output_type = 'jpeg';
    $this->library->set_input_file($this->input_file, $input_type);
    $this->library->set_output_file($this->output_file, $output_type); 
  }

	function tearDown()
	{
		if(file_exists($this->output_file))
			unlink($this->output_file);
	}
  
  function test_installed()
  {
  	$this->assertTrue($this->library->is_library_installed());
  }

  function test_resize_by_max_dimension()
  {
    $max_dimension = 200;
    $params = array('max_dimension' => $max_dimension);
    $this->library->resize($params);
    $this->library->commit();
    
    $info1 = getimagesize($this->input_file);
    $info2 = getimagesize($this->output_file);
    if ($info1[0] > $info1[1])
  	  $this->assertEqual($info2[0], $max_dimension);
    else
  	  $this->assertEqual($info2[1], $max_dimension);
  }
  
  function test_resize_by_scale_factor()
  {
    $scale_factor = 2;
    $params = array('scale_factor' => $scale_factor);
    $this->library->resize($params);
    $this->library->commit();
    
    $info1 = getimagesize($this->input_file);
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual(floor($info1[0] * $scale_factor), $info2[0]);
  	$this->assertEqual(floor($info1[1] * $scale_factor), $info2[1]);
  }

  function test_resize_by_xy_scale()
  {
    $info1 = getimagesize($this->input_file);
    $xscale = 2;
    $yscale = 1.5;
    
    $params = array('xscale' => $xscale, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual(floor($info1[0] * $xscale), $info2[0]);
  	$this->assertEqual(floor($info1[1] * $xscale), $info2[1]);

    $params = array('yscale' => $yscale, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual(floor($info1[0] * $yscale), $info2[0]);
  	$this->assertEqual(floor($info1[1] * $yscale), $info2[1]);

    $params = array('xscale' => $xscale, 'yscale' => $yscale);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual(floor($info1[0] * $xscale), $info2[0]);
  	$this->assertEqual(floor($info1[1] * $yscale), $info2[1]);
  }

  function test_resize_by_width_height()
  {
    $info1 = getimagesize($this->input_file);
    $width = 200;
    $height = 300;
    
    $params = array('width' => $width, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], $width);
  	$this->assertEqual($info2[1], floor($info1[1] * $width / $info1[0]));

    $params = array('height' => $height, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], floor($info1[0] * $height / $info1[1]));
  	$this->assertEqual($info2[1], $height);

    $params = array('width' => $width, 'height' => $height);
    $this->library->resize($params);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], $width);
  	$this->assertEqual($info2[1], $height);
  }

/*  function test_rotate()
  {
    $angle = 30;
    
    $this->library->rotate($angle, '000000');
    $this->library->commit();
    
//      $this->assertEqual(filesize($this->output_file), $this->rotated_size);
    clearstatcache();
  }*/
  
  function test_flip()
  {
    $info1 = getimagesize($this->input_file);
     
    $this->library->flip(FLIP_HORIZONTAL);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info1[0], $info2[0]);
  	$this->assertEqual($info1[1], $info2[1]);
//      $this->assertEqual(filesize($this->output_file), $this->hflipped_size);
    clearstatcache();

    $this->library->flip(FLIP_VERTICAL);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info1[0], $info2[0]);
  	$this->assertEqual($info1[1], $info2[1]);
//      $this->assertEqual(filesize($this->output_file), $this->wflipped_size);
    clearstatcache();
  }
  
  function test_cut_inside()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = 10;
    $y = 10;
    $w = 50;
    $h = 50;
    
    //$this->library->rotate(30, $bgcolor);
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
	
	if(!file_exists($this->output_file))
		echo $this->output_file . '<br>';
	
  	$this->assertEqual($info2[0], $w, __LINE__ . ' %s');
  	$this->assertEqual($info2[1], $h, __LINE__ . ' %s');
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size1);
    clearstatcache();
  }

  function test_cut_outside()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = -10;
    $y = -10;
    $w = 200;
    $h = 200;
    
    //$this->library->rotate(30, $bgcolor);
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], $w);
  	$this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size2);
    clearstatcache();
  }

  function test_cut_left_up()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = -10;
    $y = -10;
    $w = 50;
    $h = 50;
    
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], $w);
  	$this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size3);
    clearstatcache();
  }

  function test_cut_right_down()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = 50;
    $y = 50;
    $w = 100;
    $h = 100;
    
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();
    
    $info2 = getimagesize($this->output_file);
  	$this->assertEqual($info2[0], $w);
  	$this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size4);
    clearstatcache();
  }
}
?>