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

class stats_referer
{	
	var $db = null;
	
	function stats_referer()
	{
		$this->db =& db_factory :: instance();
	}

	function get_referer_page_id()
	{
		if ($result = $this->_get_existing_referer_record_id())
			return $result;
		else
			return $this->_insert_referer_record();
	}	

	function _get_clean_referer_page()
	{
		if (isset($_SERVER['HTTP_REFERER']))
			return $this->_clean_url($_SERVER['HTTP_REFERER']);
	}

	function _get_existing_referer_record_id()
	{
		$this->db->sql_select('sys_stat_referer_url', '*', 
			"referer_url='" . $this->_get_clean_referer_page() . "'");
		if ($referer_data = $this->db->fetch_row())
			return $referer_data['id'];
		else
			return false;	
	}
	
	function _insert_referer_record()
	{
		$this->db->sql_insert('sys_stat_referer_url', 
			array('referer_url' => $this->_get_clean_referer_page()));
		return $this->db->get_sql_insert_id('sys_stat_referer_url');		
	}

	function _clean_url($raw_url)
	{
		$url = trim($raw_url);
		$url = preg_replace('/(^' . preg_quote('http://' . $_SERVER['HTTP_HOST'], '/') . ')(.*)/', '\\2', $url);
		$url = preg_replace('/#[^\?]*/', '', $url);
		$url = $this->_trim_url_params($url);
		return $url;
	}
	
	function _trim_url_params($url)
	{
		if(strpos($url, '?') !== false)
		{
			$url = preg_replace('/PHPSESSID=[^&]*/', '', $url);
						
			if($pos == (strlen($url)-1))
				$url = rtrim($url, '?');
		}
		$url = rtrim($url, '/');
	}
}

?>