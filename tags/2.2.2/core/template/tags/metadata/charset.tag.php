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
class metadata_charset_tag_info
{
	var $tag = 'metadata:CHARSET';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'metadata_charset_tag';
} 

register_tag(new metadata_charset_tag_info());

class metadata_charset_tag extends compiler_directive_tag
{

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		//<meta http-equiv="Content-Type" content="text/html; charset=' . locale . '">
		$locale = '$' . $code->get_temp_variable();
		
		$code->write_php($locale . ' =& locale :: instance(CONTENT_LOCALE_ID);');
		$code->write_php("echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=' . {$locale}->get_charset() . '\">';");
	} 
} 

?>