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

require_once(LIMB_DIR . '/class/template/tags/form/select.tag.php');

class select_time_tag_info
{
	var $tag = 'time';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'select_time_tag';
} 

class select_hour_tag_info
{
	var $tag = 'hour';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'select_hour_tag';
} 

class select_minute_tag_info
{
	var $tag = 'minute';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'select_minute_tag';
} 

class select_second_tag_info
{
	var $tag = 'second';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'select_second_tag';
} 

register_tag(new select_time_tag_info());
register_tag(new select_hour_tag_info());
register_tag(new select_minute_tag_info());
register_tag(new select_second_tag_info());

class select_hour_tag extends select_tag
{
	var $runtime_component_path = '/class/template/components/form/select_time_component';

	/**
	* 
	* @var object 
	* @access protected 
	*/
	var $select_hour_object_ref_code;

	/**
	* 
	* @var object 
	* @access protected 
	*/
	var $sel_component;

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		if ($this->has_attribute('name'))
		{
			$this->remove_attribute('name');
		} 

		$code->write_html('<select name="');

		$code->write_php('echo ' . $this->sel_component . '->group_name;');

		$code->write_php('if (' . $this->sel_component . '->as_array)');

		$code->write_php('{ echo "[hour]"; } else { echo "_hour"; }');

		$code->write_html('"');

		//$this->generate_attribute_list($code, array('name', 'group_name', 'as_array'));

		$code->write_html('>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_html('</select>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$code->write_php('$' . $this->select_hour_object_ref_code . '->render_contents();');
	} 
} 

class select_minute_tag extends select_tag
{
	var $runtime_component_path = '/class/template/components/form/select_time_component';

	/**
	* 
	* @var object 
	* @access protected 
	*/
	var $select_minute_object_ref_code;

	/**
	* 
	* @var object 
	* @access protected 
	*/

	var $sel_component;

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/

	function pre_generate(&$code)
	{ 
		if ($this->has_attribute('name'))
		{
			$this->remove_attribute('name');
		} 

		$code->write_html('<select name="');

		$code->write_php('echo ' . $this->sel_component . '->group_name;');

		$code->write_php('if (' . $this->sel_component . '->as_array)');

		$code->write_php('{ echo "[minute]"; } else { echo "_minute"; }');

		$code->write_html('"');

		//$this->generate_attribute_list($code, array('name', 'group_name', 'as_array'));

		$code->write_html('>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_html('</select>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$code->write_php('$' . $this->select_minute_object_ref_code . '->render_contents();');
	} 
} 

class select_second_tag extends select_tag
{
	var $runtime_component_path = 'template/components/select_time_component';

	/**
	* 
	* @var object 
	* @access protected 
	*/
	var $select_second_object_ref_code;

	/**
	* 
	* @var object 
	* @access protected 
	*/
	var $sel_component;

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{ 
		if ($this->has_attribute('name'))
		{
			$this->remove_attribute('name');
		} 

		$code->write_html('<select name="');

		$code->write_php('echo ' . $this->sel_component . '->group_name;');

		$code->write_php('if (' . $this->sel_component . '->as_array)');

		$code->write_php('{ echo "[second]"; } else { echo "_second"; }');

		$code->write_html('"');

		//$this->generate_attribute_list($code, array('name', 'group_name'));

		$code->write_html('>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_html('</select>');
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$code->write_php('$' . $this->select_second_object_ref_code . '->render_contents();');
	} 
} 

class select_time_tag extends core_block_tag
{

	var $runtime_component_path = '/class/template/components/form/select_time_component';

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_hour(&$code)
	{
		$select_hour_object_ref_code = get_new_server_id();

		$select_hour = &$this->find_child_by_class('select_hour_tag');

		$select_hour->sel_component = $this->get_component_ref_code();

		$select_hour->select_hour_object_ref_code = $select_hour_object_ref_code;

		$select_hour->attributes['group_name'] = $this->attributes['name'];

		$code->write_php($this->get_component_ref_code() . '->prepare_hour();');

		$code->write_php('$' . $select_hour_object_ref_code . '=' . $this->get_component_ref_code() . '->get_hour();');

		$select_hour->pre_generate($code);

		$select_hour->generate_contents($code);

		$select_hour->post_generate($code);
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_minute(&$code)
	{
		$select_minute_object_ref_code = get_new_server_id();

		$select_minute = &$this->find_child_by_class('select_minute_tag');

		$select_minute->sel_component = $this->get_component_ref_code();

		$select_minute->select_minute_object_ref_code = $select_minute_object_ref_code;

		$select_minute->attributes['group_name'] = $this->attributes['name'];

		$code->write_php($this->get_component_ref_code() . '->prepare_minute();');

		$code->write_php('$' . $select_minute_object_ref_code . '=' . $this->get_component_ref_code() . '->get_minute();');

		$select_minute->pre_generate($code);

		$select_minute->generate_contents($code);

		$select_minute->post_generate($code);
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_second(&$code)
	{
		$select_second_object_ref_code = get_new_server_id();

		$select_second = &$this->find_child_by_class('select_second_tag');

		$select_second->sel_component = $this->get_component_ref_code();

		$select_second->select_second_object_ref_code = $select_second_object_ref_code;

		$select_second->attributes['group_name'] = $this->attributes['name'];

		$code->write_php($this->get_component_ref_code() . '->prepare_second();');

		$code->write_php('$' . $select_second_object_ref_code . '=' . $this->get_component_ref_code() . '->get_second();');

		$select_second->pre_generate($code);

		$select_second->generate_contents($code);

		$select_second->post_generate($code);
	} 

	/**
	* 
	* @param code_writer $ 
	* @return void 
	* @access protected 
	*/
	function generate_contents(&$code)
	{
		$function_map = array(
		    'select_hour_tag'   => 'generate_hour',
		    'select_minute_tag' => 'generate_minute',
		    'select_second_tag' => 'generate_second');

		$code->write_php($this->get_component_ref_code() . '->set_group_name("' . $this->attributes['name'] . '");');

		$code->write_php($this->get_component_ref_code() . '->set_as_array();');

		foreach ($this->children as $key => $child)
		{
			$child_class = get_class($child);
			
 			if (in_array($child_class, array_keys($function_map)))
      	$this->$function_map[$child_class]($code);
      else
      	$this->children[$key]->generate($code);
		} 
	} 
} 

?>