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
  var $referer_register;
  var $uri_register;

  function StatsHitRegister()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsHit');
  }

  function register(&$stats_request)
  {
    $referer_register =& $this->getRefererRegister();
    $uri_register =& $this->getUriRegister();

    $this->db_table->insert(
      array(
        'ip' => Ip :: decode($stats_request->getClientIp()),
        'time' => $stats_request->getTime(),
        'stats_referer_id' => $referer_register->getId($stats_request->getRefererUri()),
        'stats_uri_id' => $uri_register->getId($stats_request->getUri()),
        'session_id' => session_id(),
        'action' => $stats_request->getAction(),
      )
    );
  }

  function cleanUntil($time)
  {
    $this->db_table->delete(array('time < ' . $time));
  }

  function setRefererRegister(&$referer)
  {
    $this->referer_register =& $referer;
  }

  function setUriRegister(&$uri)
  {
    $this->uri_register =& $uri;
  }

  function & getRefererRegister()
  {
    if (is_object($this->referer_register))
      return $this->referer_register;

    include_once(dirname(__FILE__) . '/StatsReferer.class.php');
    $this->referer_register = new StatsReferer();

    return $this->referer_register;
  }

  function & getUriRegister()
  {
    if (is_object($this->uri_register))
      return $this->uri_register;

    include_once(dirname(__FILE__) . '/StatsUri.class.php');
    $this->uri_register = new StatsUri();

    return $this->uri_register;
  }
}


?>