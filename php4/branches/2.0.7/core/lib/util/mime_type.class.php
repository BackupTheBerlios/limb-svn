<?
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class mime_type
{
	var $mime_types = array();
	var $is_object = false;

	function mime_type()
	{
		$this->mime_types = array(
				'application/msword' => array(
						'icon' => 'application_doc',
				),
				'application/vnd.ms-excel' => array(
						'icon' => 'application_xsl',
				),
				'application/vnd.ms-powerpoint' => array(
						'icon' => 'application_ppt',
				),
				'application/octet-stream' => array(
						'icon' => 'file',
				),
				'application/pdf' => array(
						'icon' => 'application_pdf',
				),
				'application/zip' => array(
						'icon' => 'application_zip',
				),
				'application/x-shockwave-flash' => array(
						'icon' => 'application_swf',
				),
				'application/x-zip-compressed' => array(
						'icon' => 'application_zip',
				),
				'application/x-gzip-compressed' => array(
						'icon' => 'application_zip',
				),
				'audio/x-wav' => array(
						'icon' => 'audio_mpeg',
				),
				'audio/mpeg' => array(
						'icon' => 'audio_mpeg',
				),
				'image/bmp' => array(
						'icon' => 'image_bmp',
				),
				'image/gif' => array(
						'icon' => 'image_gif',
				),
				'image/jpeg' => array(
						'icon' => 'image_jpeg',
				),
				'image/pjpeg' => array(
						'icon' => 'image_pjpeg',
				),
				'image/png' => array(
						'icon' => 'image_png',
				),
				'image/psd' => array(
						'icon' => 'image_psd',
				),
				'message/rfc822' => array(
						'icon' => 'message_rfc822',
				),
				'text/html' => array(
						'icon' => 'file',
				),
				'text/plain' => array(
						'icon' => 'file',
				),
				'text/rtf' => array(
						'icon' => 'application_doc',
				),
				'video/avi' => array(
						'icon' => 'video_mpeg',
				),
				'video/mpeg' => array(
						'icon' => 'video_mpeg',
				),
				'default' => array(
						'icon' => 'file',
				)
		);
	}

	function get_type_icon($mime_type, $image_size = 16)
	{
		if(!$this->is_object)
			$object =& new mime_type();
		
		if(!array_key_exists($mime_type, $object->mime_types))
			$mime_type = 'default' ;

		return '/shared/images/mime_icons/'. $object->mime_types[$mime_type]['icon'].'.'.$image_size.'.gif' ;
	}
}
?>