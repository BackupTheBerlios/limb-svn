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

/**
* Ancester tag class for input controls
*/
class control_tag extends server_tag_component_tag
{
  
	/**
	* Returns the identifying server ID. It's value it determined in the
	* following order;
	* <ol>
	* <li>The XML id attribute in the template if it exists</li>
	* <li>The value of $this->server_id</li>
	* <li>An ID generated by the get_new_server_id() function</li>
	* </ol>
	* 
	* @see get_new_server_id
	* @return string value identifying this component
	* @access protected 
	*/
	function get_server_id()
	{
		if (!empty($this->attributes['id']))
		{
			return $this->attributes['id'];
		} 
		elseif (!empty($this->server_id))
		{
			return $this->server_id;
		} 
		else
		{
			$this->server_id = get_new_server_id();
			return $this->server_id;
		} 
	} 

	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class(get_class($this)))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!$this->find_parent_by_class('form_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'form',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		parent :: generate_constructor($code);
						
		if (array_key_exists('display_name', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->display_name = \'' . $this->attributes['display_name'] . '\';');
			unset($this->attributes['display_name']);
		} 
	} 
		
	function post_generate(&$code)
	{
		parent :: post_generate($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_js_validation();');
		$code->write_php($this->get_component_ref_code() . '->render_errors();');
	}
} 

?>