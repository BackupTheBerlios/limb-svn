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

class display_file_action extends action
{	
	function perform(&$request, &$response)
	{
		$object_data =& fetch_requested_object($request);
		
		if(!file_exists(MEDIA_DIR . $object_data['media_id'] . '.media'))
		{
			$response->header("HTTP/1.1 404 Not found");
			
			if(isset($_GET['icon']))
				$response->commit(); //for speed
			else
			{
			  $request->set_status(REQUEST_STATUS_FAILURE);
				return;
			}
		}
		
		if (isset($_GET['icon']))
		{
			$size = 16;
			if (!empty($_GET['icon']))
				$size = $_GET['icon'];
			
			$mime_type = $object_data['mime_type'];
			
			if($mime_type == 'application/x-zip-compressed')
				$mime_type = 'application/zip';
			elseif($mime_type == 'application/x-shockwave-flash')
				$mime_type = 'application/swf';
			
			$file_name = SHARED_DIR . 'images/mime_icons/' . str_replace('/', '_' , $mime_type) . '.' . $size . '.gif';

			if (!file_exists($file_name))
				$file_name = SHARED_DIR . "images/mime_icons/file.{$size}.gif";
			
			$response->header("Date: " . gmdate("D, d M Y H:i:s") . " GMT");
			$response->header("Etag: {$etag}");			
			$response->header("Content-type: image/gif");
			$response->readfile($file_name);			
			
			$response->commit();//for speed	
		}

		$response->header("Date: " . gmdate("D, d M Y H:i:s") . " GMT");
		$response->header("Content-type: {$object_data['mime_type']}");
		$response->header('Content-Disposition: attachment; filename="' . $object_data['file_name'] . '"');
		$response->readfile(MEDIA_DIR . $object_data['media_id'] . '.media');
	}
}

?>