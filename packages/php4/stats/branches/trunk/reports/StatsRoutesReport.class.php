<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/db/LimbDbPool.class.php');

class StatsRoutesReport //implements StatsReportInterface
{
  var $db;
  var $start_date = null;
  var $finish_date = null;

  var $routes_array = array();
  var $condition_changed = true;

  function StatsRoutesReport()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function fetch($params = array())
  {
    $records = $this->_retrieveRoutes();

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $records = array_splice($records, $offset, $limit);

    return $records;
  }

  function fetchCount($params = array())
  {
    return sizeof($this->_retrieveRoutes());
  }

  function setPeriodFilter($start_date, $finish_date)
  {
    static $prev_start_date = null;
    static $prev_finish_date = null;

    if($prev_start_date &&  $prev_finish_date)
    {
      if(!$start_date->isEqual($prev_start_date) ||  !$finish_date->isEqual($prev_funish_date))
        $this->condition_changed = true;
      else
        $this->condition_changed = false;
    }

    $prev_start_date = $start_date;
    $prev_finish_date = $finish_date;

    $this->start_date = $start_date;
    $this->finish_date = $finish_date;
  }

  function _retrieveRoutes()
  {
    if(!$this->condition_changed)
      return $this->routes_array;

    $start_stamp = $this->start_date->getStamp();
    $finish_stamp = $this->finish_date->getStamp();

    $toolkit =& Limb :: toolkit();
    $tree =& $toolkit->getTree();
    $root = $tree->getNodeByPath('/root');
    $root_id = $root['id'];

    $sql = "SELECT sslog.time as time, sslog.action as action, sslog.session_id as session_id, ssu.uri as uri
            FROM sys_stat_log sslog, sys_stat_uri ssu
            WHERE
            sslog.stat_uri_id = ssu.id AND
            ((sslog.node_id={$root_id} AND ssu.uri='/root') OR (ssu.uri != '/root')) AND
            sslog.time BETWEEN {$start_stamp} AND {$finish_stamp} AND
            sslog.user_id = -1
            ORDER BY sslog.session_id, sslog.time ASC";

    $this->db->sqlExec($sql);

    $session_events = array();
    $session_routes = array();
    $prev_session_id = -1;

    while($record = $this->db->fetchRow())
    {
      $session_id = $record['session_id'];

      if($prev_session_id != $session_id)
      {
        $counter = 0;
        $session_rotes[$session_id] = '';
        $prev_record = array('uri' => '', 'action' => '');
      }

      $prev_session_id = $session_id;

      if($counter > 10)
        continue;

      if(($prev_record['uri'] != $record['uri'] ||  $prev_record['action'] != $record['action']))
      {
        $session_events[$session_id][] = $record;
        $prev_record = $record;
        $session_rotes[$session_id] .= '=>'. $record['uri'] .'***'. $record['action'];
        $counter++;
      }
    }

    $unique_routes = array_count_values($session_rotes);
    arsort($unique_routes);

    $total_routes = array_sum($unique_routes);

    $this->routes_array = array();
    $i = 0;
    foreach($unique_routes as $route => $hits)
    {
      $pieces = explode('=>', $route);
      array_shift($pieces);
      for($level=0; $level < sizeof($pieces); $level++)
      {
        list($uri, $action) = explode('***', $pieces[$level]);

        $this->routes_array[$i]['route'][$level] = array('uri' => $uri, 'action' => $action, 'level' => $level);
      }

      $this->routes_array[$i]['hits'] = $hits;
      $this->routes_array[$i++]['percentage'] = round($hits/$total_routes * 100 , 2);
    }

    return $this->routes_array;
  }
}

?>
