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

	function mime_type()
	{
		$this->mime_types = array(
				'application/msword' => array(
						'icon' => 'application_doc',
						'extension' => 'doc'
				),
				'application/vnd.ms-excel' => array(
						'icon' => 'application_xsl',
						'extension' => 'xls'
				),
				'application/vnd.ms-powerpoint' => array(
						'icon' => 'application_ppt',
						'extension' => 'ppt'
				),
				'application/pdf' => array(
						'icon' => 'application_pdf',
						'extension' => 'pdf'
				),
				'application/zip' => array(
						'icon' => 'application_zip',
						'extension' => 'zip'
				),
				'application/x-shockwave-flash' => array(
						'icon' => 'application_swf',
						'extension' => 'swf'
				),
				'application/x-zip-compressed' => array(
						'icon' => 'application_zip',
						'extension' => 'zip'
				),
				'audio/x-wav' => array(
						'icon' => 'audio_mpeg',
						'extension' => 'wav'
				),
				'audio/mpeg' => array(
						'icon' => 'audio_mpeg',
						'extension' => 'mpeg'
				),
				'image/bmp' => array(
						'icon' => 'image_bmp',
						'extension' => 'bmp'
				),
				'image/gif' => array(
						'icon' => 'image_gif',
						'extension' => 'gif'
				),
				'image/jpeg' => array(
						'icon' => 'image_jpeg',
						'extension' => 'jpg'
				),
				'image/pjpeg' => array(
						'icon' => 'image_pjpeg',
						'extension' => 'jpeg'
				),
				'image/png' => array(
						'icon' => 'image_png',
						'extension' => 'png'
				),
				'image/psd' => array(
						'icon' => 'image_psd',
						'extension' => 'psd'
				),
				'message/rfc822' => array(
						'icon' => 'message_rfc822',
						'extension' => 'msg'
				),
				'text/html' => array(
						'icon' => 'file',
						'extension' => 'html'
				),
				'text/plain' => array(
						'icon' => 'file',
						'extension' => 'txt'
				),
				'text/rtf' => array(
						'icon' => 'application_doc',
						'extension' => 'rtf'
				),
				'video/avi' => array(
						'icon' => 'video_mpeg',
						'extension' => 'avi'
				),
				'video/mpeg' => array(
						'icon' => 'video_mpeg',
						'extension' => 'mpg'
				),
				'default' => array(
						'icon' => 'file',
						'extension' => 'media'
				)
		);
	}

	function get_type_icon($mime_type, $image_size = 16)
	{
		if(!array_key_exists($mime_type, $this->mime_types))
			$mime_type = 'default' ;

		return '/shared/images/mime_icons/'. $this->mime_types[$mime_type]['icon'].'.'.$image_size.'.gif' ;
	}

	function get_type_extension($mime_type)
	{
		
		if(!array_key_exists($mime_type, $this->mime_types))
			$mime_type = 'default' ;

		return  $this->mime_types[$mime_type]['extension'] ;
	}

}
?>