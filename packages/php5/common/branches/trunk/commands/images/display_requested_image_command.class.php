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
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

if(!defined('HTTP_SHARED_DIR'))
  define('HTTP_SHARED_DIR', LIMB_DIR . '/shared/');

if(!defined('MEDIA_DIR'))
  define('MEDIA_DIR', VAR_DIR . '/media/');

class display_requested_image_command implements command
{
  const DAY_CACHE = 86400;
  
	public function perform()
	{
    $request = Limb :: toolkit()->getRequest();
    $response = Limb :: toolkit()->getResponse();    
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    
    $datasource->set_request($request);
    
		if(!$object_data = $datasource->fetch())
      return Limb :: STATUS_ERROR;
    
		$variation = $this->_get_requested_variation($request);

		if(!isset($object_data['variations'][$variation]))
		{
			if($variation == 'original')
			  return Limb :: STATUS_ERROR;
			else
      {
        $response->header("Content-type: image/gif");
        $response->readfile(HTTP_SHARED_DIR . 'images/1x1.gif');        
				$response->commit();//for speed
        return;//for tests, fix!!!
      }
		}
    
    $image = $object_data['variations'][$variation];

		if(!file_exists(MEDIA_DIR. $image['media_id'] . '.media'))
		{
			if($variation == 'original')
				return Limb :: STATUS_ERROR;
			else
      {
        $response->header("HTTP/1.1 404 Not found");
				$response->commit();//for speed
      }
      return;//for tests, fix!!!
		}

		$http_cache = $this->_get_http_cache();
		$http_cache->set_last_modified_time($object_data['modified_date']);
		$http_cache->set_cache_time(self :: DAY_CACHE);
  
		if(!$http_cache->check_and_write($response))
		{
			$response->header("Content-Disposition: filename={$image['file_name']}");
			$response->readfile(MEDIA_DIR. $image['media_id'] .'.media');
		}
    
    $response->header("Content-type: {$image['mime_type']}");
    
		if($variation == 'original')
			return Limb :: STATUS_OK;
		else
			$response->commit();//for speed
    return;//for tests, fix!!!
	}

	protected function _get_http_cache()
	{
	  include_once(LIMB_DIR . '/class/core/request/http_cache.class.php');
	  return new http_cache();
	}

	protected function _get_requested_variation($request)
	{
		$ini = Limb :: toolkit()->getINI('image_variations.ini');
		
    $variation = 'thumbnail';
    $image_variations = $ini->get_all();
    
		foreach($image_variations as $key => $value)
		{
			if ($request->has_attribute($key))
			{
				$variation = $key;
				break;
			}
		}

		return $variation;
	}
}

?>