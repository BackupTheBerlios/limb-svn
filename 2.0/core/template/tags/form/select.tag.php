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


require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class select_tag_info
{
	var $tag = 'select';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'select_tag';
} 

register_tag(new select_tag_info());

/**
* Compile time component for building runtime select components
*/
class select_tag extends control_tag
{
	var $runtime_component_path;

	/**
	* 
	* @return void 
	* @access protected 
	*/
	function prepare()
	{
		if (array_key_exists('multiple', $this->attributes))
		{
			$this->runtime_component_path = '/core/template/components/form/select_multiple_component';

			if (!is_integer(strpos($this->attributes['name'], '[]')))
			{
				error('compiler', 'CONTROLARRAYREQUIRED', array('name' => $this->attributes['name'],
						'file' => $this->source_file,
						'line' => $this->starting_line_no));
			} 
		} 
		else
			$this->runtime_component_path = '/core/template/components/form/select_single_component';
	} 

	/**
	* Ignore the compiler time contents and generate the contents at run time.
	* 
	* @return void 
	* @access protected 
	*/
	// Ignore the compiler time contents and generate the contents at run time.
	function generate_contents(&$code)
	{		
		$code->write_php($this->get_component_ref_code() . '->render_contents();');
		
		parent :: generate_contents($code);
	} 
} 

?>