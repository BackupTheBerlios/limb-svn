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
require_once(LIMB_DIR . '/core/http/Uri.class.php');

class StatsRequest
{
  var $uri;
  var $base_uri;
  var $referer_uri;
  var $client_ip;
  var $time;
  var $action;

  function StatsRequest()
  {
    $this->uri = new Uri();
    $this->base_uri = new Uri();
    $this->referer_uri = new Uri();
    $this->time = time();
  }

  function isHomeHit()
  {
    return $this->base_uri->compare($this->uri);
  }

  function isAudienceHit()
  {
    $string = $this->referer_uri->toString();
    return empty($string);
  }

  function setUri($uri)
  {
    $this->uri = $uri;
  }

  function & getUri()
  {
    return $this->uri;
  }

  function setBaseUri($uri)
  {
    $this->base_uri = $uri;
  }

  function & getBaseUri()
  {
    return $this->base_uri;
  }

  function setRefererUri($uri)
  {
    $this->referer_uri = $uri;
  }

  function & getRefererUri()
  {
    return $this->referer_uri;
  }

  function setClientIp($ip)
  {
    $this->client_ip = $ip;
  }

  function setTime($time)
  {
    $this->time = $time;
  }

  function getClientIp()
  {
    return $this->client_ip;
  }

  function getTime()
  {
    return $this->time;
  }

  function getAction()
  {
    return $this->action;
  }

  function setAction($action)
  {
    $this->action = $action;
  }
}

?>