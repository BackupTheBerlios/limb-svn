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


class pager_navigator_tag_info
{
	var $tag = 'pager:NAVIGATOR';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'pager_navigator_tag';
} 

register_tag(new pager_navigator_tag_info());

/**
* Compile time component for root of a pager tag
*/
class pager_navigator_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/pager_component';

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);

		$code->write_php($this->get_component_ref_code() . '->prepare();');
	} 
	
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		parent::generate_constructor($code);

		if (array_key_exists('items', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->items = \'' . $this->attributes['items'] . '\';');
			unset($this->attributes['items']);
		} 
		if (array_key_exists('pages_per_section', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->pages_per_section = \'' . $this->attributes['pages_per_section'] . '\';');
			unset($this->attributes['pages_per_section']);
		} 
	}
	
	function get_component_ref_code()
	{
		if (isset($this->attributes['mirror_of']))
		{
			if($mirrored_pager =& $this->parent->find_child($this->attributes['mirror_of']))
				return $mirrored_pager->get_component_ref_code();
			else
				debug :: write_error('mirror_of pager component not found',
				 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
				array('mirror_of' => $this->attributes['mirror_of']));
		}	
		else
			return parent :: get_component_ref_code();
	} 
} 

?>