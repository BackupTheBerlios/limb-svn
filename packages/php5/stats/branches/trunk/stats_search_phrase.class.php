<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class stats_search_phrase
{	
  static protected $instance = null;
  
	protected $db = null;
	protected $url = null;
	
	protected $engine_rules = array();
	
	public function __construct()
	{
		$this->db = db_factory :: instance();
		$this->url = new uri();
	}
	
	static public function instance()
	{
    if (!self :: $instance)
      self :: $instance = new stats_search_phrase();

    return self :: $instance;	
	}
			
	public function register_search_engine_rule($engine_rule)
	{
		$this->engine_rules[] = $engine_rule;
	}
	
	public function register($date)
	{
		if(!$rule = $this->_get_matching_search_engine_rule())
			return false;
		
		$this->db->sql_insert('sys_stat_search_phrase', 
			array(
				'engine' => $rule->get_engine_name(), 
				'time' => $date->get_stamp(),
				'phrase' => stripslashes(strip_tags($rule->get_matching_phrase())),
			)
		);
		
		return true;
	}
	
	protected function _get_matching_search_engine_rule()
	{
		$uri = urldecode($this->_get_http_referer());
		
		foreach(array_keys($this->engine_rules) as $id)
		{
			if($this->engine_rules[$id]->match($uri))
				return $this->engine_rules[$id];
		}
		return null;
	}	
	
	protected function _get_http_referer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	}
}

?>