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
	function perform(&$request, &$response)
	{
		$object_data =& fetch_requested_object($request);
		
		$ini =& get_ini('image_variations.ini');
		
		$image_variations = $ini->get_all();
		
		foreach($image_variations as $key => $value)
		{
			if (array_key_exists($key, $_GET))
			{
				$variation = $key;
				break;
			}
		}
		
		if (empty($variation))
			$variation = 'thumbnail';
		
		$image = $object_data['variations'][$variation];
		
		if(!$image)
		{
			$response->header("Content-type: image/gif");
			$response->readfile(SHARED_DIR . 'images/1x1.gif');
			
			if($variation == 'original')
			{
			  $request->set_status(REQUEST_STATUS_FAILURE);
				return;
			}
			else
			{
				$response->commit();//for speed
			}
		}		
				
		if(!file_exists(MEDIA_DIR. $image['media_id'] . '.media'))
		{
			$response->header("HTTP/1.1 404 Not found");
			
			if($variation == 'original')
			{
			  $request->set_status(REQUEST_STATUS_FAILURE);
				return;
			}
			else
			{
				$response->commit();//for speed
			}
		}		
				
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $image['etag'])
		{
			$response->use_client_cache();
			$response->header("Pragma: public");
			$response->header("Cache-Control: private");
			$response->header("Date: " . date("D, d M Y H:i:s") . " GMT");
			$response->header("Etag: {$image['etag']}");
		}
		else
		{
			$response->header("Pragma: public");
			$response->header("Cache-Control: private");
			$response->header("Date: " . date("D, d M Y H:i:s") . " GMT");
			$response->header("Etag: {$image['etag']}");
			$response->header("Content-type: {$image['mime_type']}");
			$response->header("Content-Disposition: filename={$image['file_name']}"); 
			$response->readfile(MEDIA_DIR. $image['media_id'] .'.media');
		}
		
		if($variation == 'original')
			return;
		else
		{
			$response->commit();//for speed
		}
	}
}

?>