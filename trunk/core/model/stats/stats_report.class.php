<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: site_object.class.php 20 2004-03-05 09:59:38Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class stats_report
{
	var $db = null;
	
	function stats_report()
	{
		$this->db =& db_factory :: instance();
	}
	
	function fetch($params = array())
	{
		$sql = "SELECT 
						sslog.*, 
						sso.id as object_id, 
						sso.identifier as identifier,
						sso.title as title,
						user.identifier as user_login
						FROM 
						sys_stat_log as sslog LEFT JOIN user ON user.object_id=sslog.user_id 
						LEFT JOIN sys_site_object_tree as ssot ON ssot.id=sslog.node_id
						LEFT JOIN sys_site_object as sso ON ssot.object_id=sso.id";

		if(isset($params['order']))
			$sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);
		
		$limit = isset($params['limit']) ? $params['limit'] : 0;
		$offset = isset($params['offset']) ? $params['offset'] : 0;

		$this->db->sql_exec($sql, $limit, $offset);
				
		return $this->db->get_array('id');
	}
	
	function fetch_count($params = array())
	{
		$sql = "SELECT COUNT(id) as count FROM sys_stat_log";
		
		$this->db->sql_exec($sql);
		$arr =& $this->db->fetch_row();
		return (int)$arr['count'];
	}

	function _build_order_sql($order_array)
	{
		$columns = array();
		
		foreach($order_array as $column => $sort_type)
			$columns[] = $column . ' ' . $sort_type;
			
		return implode(', ', $columns);
	}	
}

?>
