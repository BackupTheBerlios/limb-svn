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

require_once(LIMB_DIR . '/core/lib/http/uri.class.php');

class stats_uri
{	
	var $db = null;
	var $url = null;
	
	function stats_uri()
	{
		$this->db =& db_factory :: instance();
		$this->url = new uri();
	}

	function get_uri_id()
	{
		$uri = $this->clean_url($this->_get_http_uri());
		
		if ($result = $this->_get_existing_uri_record_id($uri))
			return $result;
			
		return $this->_insert_uri_record($uri);
	}	
	
	function _get_http_uri()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}
	
	function _get_existing_uri_record_id($uri)
	{
		$this->db->sql_select('sys_stat_uri', '*', 
			"uri='" . $uri . "'");
		if ($uri_data = $this->db->fetch_row())
			return $uri_data['id'];
		else
			return false;	
	}
	
	function _insert_uri_record($uri)
	{
		$this->db->sql_insert('sys_stat_uri', 
			array('uri' => $uri));
		return $this->db->get_sql_insert_id('sys_stat_uri');		
	}

	function clean_url($raw_url)
	{
		$this->url->parse($raw_url);
		
		$this->url->remove_query_items();
		$this->url->remove_anchor();
							
		if($this->url->is_inner())
			return $this->url->get_inner_url();
		else
			return $this->url->get_url();
	}	
}

?>