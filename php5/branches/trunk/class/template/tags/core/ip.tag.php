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
class ip_tag_info
{
	public $tag = 'core:IP';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'ip_tag';
} 

register_tag(new ip_tag_info());

class ip_tag extends compiler_directive_tag
{
	public function generate_contents($code)
	{
		if(isset($this->attributes['hash_id']))
		{
			$code->write_php(
				'echo ip :: decode_ip(' . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '"));');
		}
		else
			$code->write_php('echo sys :: client_ip();');
	} 
} 

?>