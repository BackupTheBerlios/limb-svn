<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: StatsRegister.class.php 1135 2005-03-03 10:25:19Z seregalimb $
*
***********************************************************************************/
class StatsHitRegister
{
  var $db = null;
  var $stats_referer;
  var $stats_uri;

  function StatsHitRegister()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsHit');
  }

  function register(&$stats_request)
  {
    $stats_referer =& $this->getStatsReferer();
    $stats_uri =& $this->getStatsUri();

    $this->db_table->insert(
      array(
        'ip' => Ip :: decode($stats_request->getClientIp()),
        'time' => $stats_request->getTime(),
        'stats_referer_id' => $stats_referer->getId($stats_request->getRefererUri()),
        'stats_uri_id' => $stats_uri->getId($stats_request->getUri()),
        'session_id' => session_id(),
        'action' => $stats_request->getAction(),
      )
    );
  }

  function cleanUntil($time)
  {
    $this->db_table->delete(array('time < ' . $time));
  }

  function setStatsReferer(&$referer)
  {
    $this->stats_referer =& $referer;
  }

  function setStatsUri(&$uri)
  {
    $this->stats_uri =& $uri;
  }

  function & getStatsReferer()
  {
    if (is_object($this->stats_referer))
      return $this->stats_referer;

    include_once(dirname(__FILE__) . '/StatsReferer.class.php');
    $this->stats_referer = new StatsReferer();

    return $this->stats_referer;
  }

  function & getStatsUri()
  {
    if (is_object($this->stats_uri))
      return $this->stats_uri;

    include_once(dirname(__FILE__) . '/StatsUri.class.php');
    $this->stats_uri = new StatsUri();

    return $this->stats_uri;
  }
}


?>