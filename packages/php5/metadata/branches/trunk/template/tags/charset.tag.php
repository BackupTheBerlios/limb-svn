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
class metadata_charset_tag_info
{
	public $tag = 'metadata:CHARSET';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'metadata_charset_tag';
} 

register_tag(new metadata_charset_tag_info());

class metadata_charset_tag extends compiler_directive_tag
{
	public function generate_contents($code)
	{
		$locale = '$' . $code->get_temp_variable();
		
		$code->write_php($locale . ' = Limb :: toolkit()->getLocale(CONTENT_LOCALE_ID);');
		$code->write_php("echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=' . {$locale}->get_charset() . '\">';");
	} 
} 

?>