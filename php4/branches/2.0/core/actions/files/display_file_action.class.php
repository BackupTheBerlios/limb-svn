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
	function display_file_action($name='')
	{
	}
	
	function perform()
	{
		$object_data =& fetch_mapped_by_url();
		
		ob_end_clean();
		if(!file_exists(MEDIA_DIR . $object_data['media_id'] . '.media'))
		{
			header("HTTP/1.1 404 Not found");
			exit;
		}
		
		if (isset($_GET['icon']))
		{
			$size = 16;
			if (!empty($_GET['icon']))
				$size = $_GET['icon'];
			
			header("Content-type: image/gif");
			
			$file_name = SHARED_DIR . 'images/mime_icons/' . str_replace('/', '_' , $object_data['mime_type']) . '.' . $size . '.gif';

			if (file_exists($file_name))
				readfile($file_name);
			else
				readfile(SHARED_DIR . "images/mime_icons/file.{$size}.gif");
			exit;
		}
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $object_data['etag'])
		{
			header("HTTP/1.1 304 Not modified");
			header("Pragma: public");
			header("Cache-Control: private");
			header("Date: " . date("D, d M Y H:i:s") . " GMT");
			header("Etag: {$object_data['etag']}");
		}
		else
		{
			header("Pragma: public");
			header("Cache-Control: private");
			header("Date: " . date("D, d M Y H:i:s") . " GMT");
			header("Etag: {$object_data['etag']}");
			header("Content-type: {$object_data['mime_type']}");
			header("Content-Disposition: attachment; filename=\"{$object_data["file_name"]}\"");
			readfile(MEDIA_DIR . $object_data['media_id'] . '.media');
		}
		
		exit;
	}
}
?>