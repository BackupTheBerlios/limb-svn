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

define('ENDTAG_REQUIRED', 1);
define('ENDTAG_OPTIONAL', 2);
define('ENDTAG_FORBIDDEN', 3);

/**
* Registers information about a compile time tag in the global tag dictionary.
* This function is called from the respective compile time component class
* file.
*/
function register_tag(&$taginfo)
{
	$GLOBALS['tag_dictionary']->register_tag($taginfo);
} 

/**
* The tag_dictionary, which exists as a global variable, acting as a registry
* of compile time components.
*/
class tag_dictionary
{
	/**
	* Associative array of tag_info objects
	* 
	* @var array 
	* @access private 
	*/
	var $tag_information = array();
	/**
	* Indexed array containing registered tag names
	* 
	* @var array 
	* @access private 
	*/
	var $tag_list = array();

	/**
	* Registers a tag in the dictionary, called from the global register_tag()
	* function.
	* 
	* @param object $ tag_info class
	* @return void 
	* @access protected 
	*/
	function register_tag($taginfo)
	{
		$tag = strtolower($taginfo->tag);
		$this->tag_list[] = $tag;
		$this->tag_information[$tag] = &$taginfo;
	} 

	/**
	* Gets the tag information about a given tag.
	* Called from the source_file_parser
	* 
	* @see source_file_parser
	* @param string $ name of a tag
	* @return object tag_info class
	* @access protected 
	*/
	function &gettag_info($tag)
	{
		return $this->tag_information[strtolower($tag)];
	} 

	/**
	* Gets the list of a registered tags.
	* Called from the source_file_parser
	* 
	* @see source_file_parser
	* @param string $ name of a tag
	* @return array list of tags
	* @access protected 
	*/
	function gettag_list()
	{
		return $this->tag_list;
	} 
} 

?>