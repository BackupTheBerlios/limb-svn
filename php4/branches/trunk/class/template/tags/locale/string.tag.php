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
require_once(LIMB_DIR . '/class/template/compiler/compiler_directive_tag.class.php');

class locale_string_tag_info
{
	var $tag = 'locale:STRING';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'locale_string_tag';
} 

register_tag(new locale_string_tag_info());

class locale_string_tag extends compiler_directive_tag
{

	function generate_contents(&$code)
	{
		$file = 'common';
		
		if(isset($this->attributes['file']))
			$file = $this->attributes['file']; 
		
		if(isset($this->attributes['locale_type']))
		{
			if(strtolower($this->attributes['locale_type']) == 'content')
				$locale_constant = 'CONTENT_LOCALE_ID';	
			else
				$locale_constant = 'MANAGEMENT_LOCALE_ID';	
		}
		else
			$locale_constant = 'MANAGEMENT_LOCALE_ID';	

		if(isset($this->attributes['hash_id']))
		{
			$locale_tmp = '$' . $code->get_temp_variable();
			
			$code->write_php(
				"{$locale_tmp} = " . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '");');
      
      if(defined('DEBUG_TEMPLATE_I18N_ENABLED') && constant('DEBUG_TEMPLATE_I18N_ENABLED'))
      {
        $code->write_php("
          echo '<img src=\'/shared/images/i.gif\' title=\'&#039;{$locale_tmp}&#039; from &#039;{$file}_???&#039; i18n file\'>';"
        );
      }
      
			$code->write_php("echo strings :: get({$locale_tmp}, '{$file}', constant('{$locale_constant}'));");
							
		}
		elseif(isset($this->attributes['name']))
		{
		  if(defined('DEBUG_TEMPLATE_I18N_ENABLED') && constant('DEBUG_TEMPLATE_I18N_ENABLED'))
		  {			
        $code->write_php("
          echo '<img src=\'/shared/images/i.gif\' title=\'&#039;{$this->attributes['name']}&#039; from &#039;{$file}_???&#039; i18n file\'>';"
        );
      }
		
			$code->write_php("echo strings :: get('{$this->attributes['name']}', '{$file}', constant('{$locale_constant}'));");
		}
		
				
		parent :: generate_contents($code);
	}
	

} 

?>