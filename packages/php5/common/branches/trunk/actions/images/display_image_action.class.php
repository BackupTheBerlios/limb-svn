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
require_once(LIMB_DIR . 'class/core/actions/action.class.php');

class display_image_action extends action
{	
	public function perform($request, $response)
	{	
	  $object_data = fetch_requested_object($request);
		$variation = $this->_get_variation();
		$image = $object_data['variations'][$variation];
		
		if(!$image)
		{
			$response->header("Content-type: image/gif");
			$response->readfile(HTTP_SHARED_DIR . 'images/1x1.gif');
			
			if($variation == 'original')
			{
			  $request->set_status(request :: STATUS_FAILURE);
				return;
			}
			else
				$response->commit();//for speed
		}		
		
		if(!file_exists(MEDIA_DIR. $image['media_id'] . '.media'))
		{
			$response->header("HTTP/1.1 404 Not found");
			
			if($variation == 'original')
			{
			  $request->set_status(request :: STATUS_FAILURE);
				return;
			}
			else
				$response->commit();//for speed
		}		
		
		$http_cache = $this->get_http_cache();
		$http_cache->set_last_modified_time($object_data['modified_date']);
		$http_cache->set_cache_time(60*60*24);
		
		if($http_cache->check_and_write($response))
		{
		  $response->header("Content-type: {$image['mime_type']}");
		}
		else
		{
			$response->header("Content-type: {$image['mime_type']}");
			$response->header("Content-Disposition: filename={$image['file_name']}"); 
			$response->readfile(MEDIA_DIR. $image['media_id'] .'.media');		
		}
		if($variation == 'original')
			return;
		else
			$response->commit();//for speed
	}
	
	public function get_http_cache()
	{
	  include_once(LIMB_DIR . '/class/core/request/http_cache.class.php');
	  return new http_cache();
	}
	
	protected function _get_variation()
	{
		$ini = get_ini('image_variations.ini');
		
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
			
		return $variation;
	}
}

?>