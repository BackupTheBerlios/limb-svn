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
require_once(LIMB_DIR . 'class/cache/partial_page_cache_manager.class.php');

class outputcache_component extends component
{
  protected $cache_manager = null;
  
	function __construct()
	{
		$this->cache_manager = new partial_page_cache_manager();
	}
	
	public function prepare()
	{
	  $request = request :: instance();
		$this->cache_manager->set_uri($request->get_uri());		
		$this->cache_manager->set_server_id($this->get_server_id());	
	}
  
  public function set_server_id($server_id)
  {
	  $this->cache_manager->set_server_id($this->server_id);
  }
  
	public function get()
	{
	  return $this->cache_manager->get();
	} 

	public function write($contents)
	{
	  return $this->cache_manager->write($contents);
	} 
} 

?>