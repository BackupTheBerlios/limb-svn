<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: image_factory.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
class image_factory
{
	function image_factory()
	{
	}
		
	function & create($library = 'gd', $dir = '')
	{
		if(defined('IMAGE_LIBRARY'))
			$library = IMAGE_LIBRARY; 
		
		$image_class_name = 'image_' . $library;
		
		if(isset($GLOBALS['global_' . $image_class_name]))
			$obj =& $GLOBALS['global_' . $image_class_name];
		else
			$obj = null;

  	if(get_class($obj) != $image_class_name)
  	{
		  $dir = ($dir == '') ? LIMB_DIR . '/core/lib/image/' : $dir;
		  
		  if(!file_exists($dir . $image_class_name . '.class.php'))
			  	error('image library not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  							array('library' => $library, 'dir' => $dir));
		  
		  include_once($dir . $image_class_name . '.class.php');
		  
		  $obj =& new $image_class_name();
  		$GLOBALS['global_' . $image_class_name] =& $obj;
  	}
  	
  	return $obj;
	}

}
?>