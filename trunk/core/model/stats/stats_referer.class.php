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

require_once(LIMB_DIR . '/core/lib/http/url_parser.class.php');

class stats_referer
{	
	var $db = null;
	var $url = null;
	
	function stats_referer()
	{
		$this->db =& db_factory :: instance();
		$this->url = new url_parser();
	}

	function get_referer_page_id()
	{
		if(!$clean_uri = $this->_get_clean_referer_page())
			return -1;
		
		if($this->url->is_inner())
			return -1;
			
		if ($result = $this->_get_existing_referer_record_id($clean_uri))
			return $result;
		
		return $this->_insert_referer_record($clean_uri);
	}	
	
	function _get_clean_referer_page()
	{
		if ($referer = $this->_get_http_referer())
			return $this->clean_url($referer);
			
		return false;
	}
	
	function _get_http_referer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	}
	
	function _get_existing_referer_record_id($uri)
	{
		$this->db->sql_select('sys_stat_referer_url', '*', 
			"referer_url='" . $uri . "'");
		if ($referer_data = $this->db->fetch_row())
			return $referer_data['id'];
		else
			return false;	
	}
	
	function _insert_referer_record($uri)
	{
		$this->db->sql_insert('sys_stat_referer_url', 
			array('referer_url' => $uri));
		return $this->db->get_sql_insert_id('sys_stat_referer_url');		
	}

	function clean_url($raw_url)
	{
		$this->url->parse($raw_url);
		
		$this->url->remove_query_item('PHPSESSID');
		$this->url->remove_anchor();
		
		return $this->url->get_url();
	}	
}

?>