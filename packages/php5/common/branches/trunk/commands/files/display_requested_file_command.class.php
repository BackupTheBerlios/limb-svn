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

if(!defined('HTTP_MIME_ICONS_DIR'))
  define('HTTP_MIME_ICONS_DIR', HTTP_SHARED_DIR . 'images/mime_icons/');

class display_requested_file_command implements command
{
  const DEFAULT_ICON_SIZE = 16;
  
	public function perform()
	{
    $request = Limb :: toolkit()->getRequest();
    $response = Limb :: toolkit()->getResponse();    
    $datasource = Limb :: toolkit()->getDatasource('requested_object_datasource');
    
    $datasource->set_request($request);
    
		if(!$object_data = $datasource->fetch())
      return Limb :: STATUS_ERROR;

		if(!file_exists(MEDIA_DIR . $object_data['media_id'] . '.media'))
		{
			$response->header("HTTP/1.1 404 Not found");

			if(!$request->has_attribute('icon'))
        return Limb :: STATUS_ERROR;
      
			$response->commit(); //for speed
			return;//for tests, fix!!!
		}

		if ($request->has_attribute('icon'))
		{
      $this->_fill_icon_response($response, $request, $object_data);			
			$response->commit();//for speed
      return;//for tests, fix!!!
		}

		$response->header("Content-type: {$object_data['mime_type']}");
		$response->header('Content-Disposition: attachment; filename="' . $object_data['file_name'] . '"');
		$response->readfile(MEDIA_DIR . $object_data['media_id'] . '.media');
    
    return Limb :: STATUS_OK;
	}
  
  protected function _fill_icon_response($response, $request, $object_data)
  {
    if (!$size = $request->get('icon'))
      $size = self :: DEFAULT_ICON_SIZE;
          
    $icon = $this->_get_mime_type()->get_type_icon($object_data['mime_type']);

    $file_path = HTTP_MIME_ICONS_DIR . "{$icon}.{$size}.gif";

    if (!file_exists($file_path))
      $file_path = HTTP_MIME_ICONS_DIR . "file.{$size}.gif";

    $response->header("Content-type: image/gif");
    $response->readfile($file_path);    
  }
  
  protected function _get_mime_type()
  {
    include_once(LIMB_DIR . '/class/lib/util/mime_type.class.php');
    return new mime_type();
  }
}

?>