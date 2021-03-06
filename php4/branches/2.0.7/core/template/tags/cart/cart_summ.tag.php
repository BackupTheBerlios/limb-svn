<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: data_source.tag.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
class sart_summ_tag_info
{
	var $tag = 'cart:SUMM';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'cart_summ_tag';
} 

register_tag(new sart_summ_tag_info());

class cart_summ_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/cart_summ_component';
	
	function generate_contents(&$code)
	{
		$code->write_php('echo '. $this->get_component_ref_code() . '->get_cart_summ();');
	} 	
} 

?>