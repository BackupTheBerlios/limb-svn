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
require_once(LIMB_DIR . 'core/actions/action.class.php');

class display_image_action extends action
{
	function display_image_action($name='')
	{
	}
	
	function perform()
	{
		$object_data =& fetch_mapped_by_url();
		
		ob_end_clean();
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_named_array();
		
		foreach($image_variations as $key => $value)
			if (array_key_exists($key, $_GET))
			{
				$variation = $key;
				break;
			}
		
		if (empty($variation))
			$variation = 'thumbnail';
		
		$image = $object_data['variations'][$variation];
		
		if (!$image)
		{
			header("HTTP/1.1 404 Not found");
			exit;
		}
				
		if(!file_exists(MEDIA_DIR. $image['media_id'] . '.media'))
		{
			header("HTTP/1.1 404 Not found");
			exit;
		}		
		
		if(empty($image))
		{
			header("Content-type: image/gif");
			readfile(SHARED_DIR . 'images/1x1.gif');
			exit;
		}		
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $image['etag'])
		{
			header("HTTP/1.1 304 Not modified");
			header("Pragma: public");
			header("Cache-Control: private");
			header("Date: " . date("D, d M Y H:i:s") . " GMT");
			header("Etag: {$image['etag']}");
		}
		else
		{
			header("Pragma: public");
			header("Cache-Control: private");
			header("Date: " . date("D, d M Y H:i:s") . " GMT");
			header("Etag: {$image['etag']}");
			header("Content-type: {$image['mime_type']}");
			header("Content-Disposition: filename={$image['file_name']}"); 
			readfile(MEDIA_DIR. $image['media_id'] .'.media');
		}
		exit;
	}
}
?>