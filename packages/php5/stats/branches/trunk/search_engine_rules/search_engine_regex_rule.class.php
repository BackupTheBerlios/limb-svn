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
require_once(dirname(__FILE__) . '/search_engine_rule.interface.php');

class search_engine_regex_rule implements search_engine_rule
{	
	private $engine_name = '';
	private $regex = '';
	private $matches = array();
	private $uri = '';
	
	private $match_phrase_index;
	
	function __construct($engine_name, $regex, $match_phrase_index)
	{
		$this->engine_name = $engine_name;
		$this->regex = $regex;
		$this->match_phrase_index = $match_phrase_index;
	}
	
	public function match($uri)
	{	
		$this->uri = $uri;
		return preg_match($this->regex, $this->uri, $this->matches);
	}	
	
	public function get_matching_phrase()
	{
		return $this->matches[$this->match_phrase_index];
	}

	public function get_engine_name()
	{
		return $this->engine_name;
	}
}

?>