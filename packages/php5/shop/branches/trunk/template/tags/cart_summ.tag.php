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
class cart_summ_tag_info
{
	public $tag = 'cart:SUMM';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'cart_summ_tag';
} 

register_tag(new cart_summ_tag_info());

class cart_summ_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../components/cart_summ_component';
	}
	
	public function generate_contents($code)
	{
		$code->write_php('echo '. $this->get_component_ref_code() . '->get_cart_summ();');
	} 	
} 

?>