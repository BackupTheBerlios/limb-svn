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

class search_engine_regex_rule
{	
	var $engine_name = '';
	var $regex = '';
	var $matches = array();
	var $uri = '';
	
	var $match_phrase_index;
	
	function search_engine_regex_rule($engine_name, $regex, $match_phrase_index)
	{
		$this->engine_name = $engine_name;
		$this->regex = $regex;
		$this->match_phrase_index = $match_phrase_index;
	}
	
	function match($uri)
	{	
		$this->uri = $uri;
		return preg_match($this->regex, $this->uri, $this->matches);
	}	
	
	function get_matching_phrase()
	{
		return $this->matches[$this->match_phrase_index];
	}

	function get_engine_name()
	{
		return $this->engine_name;
	}
	
}

?>