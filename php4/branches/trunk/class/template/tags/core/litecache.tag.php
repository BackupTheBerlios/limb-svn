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
class core_litecache_tag_info
{
	var $tag = 'core:LITECACHE';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_litecache_tag';
} 

register_tag(new core_litecache_tag_info());

class core_litecache_tag extends server_component_tag
{
	var $runtime_component_path = '/class/template/components/litecache_component';

	/**
	* Name of runtime variable reference where cached content is stored
	* 
	* @var string 
	* @access private 
	*/
	var $content_ref;
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function generate_constructor(&$code)
	{
		$code->register_include(LIMB_DIR . $this->runtime_component_path . '.class.php');
		
		$args = '__FILE__.\'' . $this->get_server_id() . '\'';
		
		isset ($this->attributes['expires']) ? 
			$args .= ',' . $this->attributes['expires'] . '' : $args .= ',3600';
		
		isset ($this->attributes['cacheby']) ? 
			$args .= ',\'' . $this->attributes['cacheby'] . '\'' : $args .= ',\'\'';
		
		isset ($this->attributes['cachegroup']) ? 
			$args .= ',\'' . $this->attributes['cachegroup'] . '\'' : $args .= ',false';

		$component_class_name = end(explode('/', $this->runtime_component_path));
		
		$code->write_php($this->parent->get_component_ref_code() . '->add_child(new ' . $component_class_name . '(' . $args . '), \'' . $this->get_server_id() . '\');');
		
		compiler_component::generate_constructor($code);
	} 
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		$this->content_ref = get_new_server_id();
		parent::pre_generate($code);
		$code->write_php('if (!' . $this->get_component_ref_code() . '->is_cached()) {');
		$code->write_php('ob_start();');
	} 
	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function post_generate(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->cache(ob_get_contents());ob_end_clean();');
		$code->write_php('}');
		$code->write_php($this->get_component_ref_code() . '->render();');
		parent::post_generate($code);
	} 
} 

?>