<?php
class core_dataspace_tag_info
{
	var $tag = 'core:DATASPACE';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'core_dataspace_tag';
} 

register_tag(new core_dataspace_tag_info());

/**
* Dataspaces act is "namespaces" for a template.
*/
class core_dataspace_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/dataspace_component';

	/**
	* 
	* @param code $ _writer
	* @return void 
	* @access protected 
	*/
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		$code->write_php($this->get_dataspace_ref_code() . '->prepare();');
	} 

	/**
	* Return this instance of the dataspace
	* 
	* @return object 
	* @access protected 
	*/
	function &get_dataspace()
	{
		return $this;
	} 

	/**
	* Get the code (the PHP reference variable) for the dataspace
	* 
	* @return string 
	* @access protected 
	*/
	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>