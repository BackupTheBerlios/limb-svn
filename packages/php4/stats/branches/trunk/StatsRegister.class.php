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
include(dirname(__FILE__) . '/search_engines.setup.php');//ugly ???

class StatsRegister
{
  var $_counter_register = null;
  var $_ip_register = null;
  var $_uri_register = null;
  var $_referer_register = null;
  var $_search_phrase_register = null;
  var $_reg_date;
  var $db = null;

  function StatsRegister()
  {
    $this->_reg_date = new Date();

    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function getRegisterTimeStamp()
  {
    return $this->_reg_date->getStamp();
  }

  function setRegisterTime($stamp = null)
  {
    if(!$stamp)
      $stamp = time();

    $this->_reg_date->setByStamp($stamp);
  }

  function register($node_id, $action, $status_code)
  {
    $this->_updateLog($node_id, $action, $status_code);

    $this->_updateCounters();

    $this->_updateSearchReferers();
  }

  function _updateLog($node_id, $action, $status_code)
  {
    $ip_register =& $this->_getIpRegister();

    $referer_register =& $this->_getRefererRegister();
    $uri_register =& $this->_getUriRegister();

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $this->db->sqlInsert('sys_stat_log',
      array(
        'ip' => $ip_register->getClientIp(),
        'time' => $this->getRegisterTimeStamp(),
        'node_id' => $node_id,
        'stat_referer_id' => $referer_register->getRefererPageId(),
        'stat_uri_id' => $uri_register->getUriId(),
        'user_id' => $user->getId(),
        'session_id' => session_id(),
        'action' => $action,
        'status' => $status_code,
      )
    );
  }

  function cleanUntil($date)
  {
    $this->db->sqlDelete('sys_stat_log', 'time < ' . $date->getStamp());
  }

  function countLogRecords()
  {
    $this->db->sqlExec('SELECT COUNT(id) as counter FROM sys_stat_log');
    $row = $this->db->fetchRow();
    return $row['counter'];
  }

  function _updateCounters()
  {
    $ip_register =& $this->_getIpRegister();
    $counter_register =& $this->_getCounterRegister();

    $counter_register->setNewHost($ip_register->isNewHost($this->_reg_date));
    $counter_register->update($this->_reg_date);
  }

  function _updateSearchReferers()
  {
    $phrase_register = $this->_getSearchPhraseRegister();
    $phrase_register->register($this->_reg_date);
  }

  function _getIpRegister()
  {
    if ($this->_ip_register)
      return $this->_ip_register;

    include_once(dirname(__FILE__) . '/StatsIp.class.php');
    $this->_ip_register = new StatsIp();

    return $this->_ip_register;
  }

  function _getCounterRegister()
  {
    if ($this->_counter_register)
      return $this->_counter_register;

    include_once(dirname(__FILE__) . '/StatsCounter.class.php');
    $this->_counter_register = new StatsCounter();

    return $this->_counter_register;
  }

  function _getRefererRegister()
  {
    if ($this->_referer_register)
      return $this->_referer_register;

    include_once(dirname(__FILE__) . '/StatsReferer.class.php');
    $this->_referer_register = new StatsReferer();

    return $this->_referer_register;
  }

  function _getUriRegister()
  {
    if ($this->_uri_register)
      return $this->_uri_register;

    include_once(dirname(__FILE__) . '/StatsUri.class.php');
    $this->_uri_register = new StatsUri();

    return $this->_uri_register;
  }

  function & _getSearchPhraseRegister()
  {
    if ($this->_search_phrase_register)
      return $this->_search_phrase_register;

    include_once(dirname(__FILE__) . '/StatsSearchPhrase.class.php');
    $this->_search_phrase_register =& StatsSearchPhrase :: instance();

    return $this->_search_phrase_register;
  }
}

?>