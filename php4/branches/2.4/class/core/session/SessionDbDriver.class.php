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
require_once(LIMB_DIR . '/class/core/session/SessionDriver.interface.php');

class SessionDbDriver implements SessionDriver
{
  var $db;
  var $user;

  function SessionDbDriver()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
    $this->user =& $toolkit->getUser();
  }

  function storageOpen()
  {
    return true;
  }

  function storageClose()
  {
    return true;
  }

  function storageRead($session_id)
  {
    $this->db->sqlSelect('sys_session', 'session_data', array('session_id' => $session_id));

    if($data = $this->db->fetchRow())
    {
      return $data['session_data'];
    }
    else
      return false;
  }

  function storageWrite($session_id, $value)
  {
    $this->db->sqlSelect('sys_session', 'session_id', array('session_id' => $session_id));

    $session_data = array('last_activity_time' => time(),
                          'session_data' => "{$value}");

    if($this->db->fetchRow())
      $this->db->sqlUpdate('sys_session', $session_data, array('session_id' => $session_id));
    else
    {
      $session_data['session_id'] = "{$session_id}";  //type juggling to string
      $session_data['user_id'] = $this->user->getId();

      $this->db->sqlInsert('sys_session', $session_data);
    }
  }

  function storageDestroy($session_id)
  {
    $this->db->sqlDelete('sys_session', array('session_id' => $session_id));
  }

  function storageGc($max_life_time)
  {
    $this->db->sqlDelete('sys_session', "last_activity_time < ". (time() - $max_life_time));
  }

  function storageDestroyUser($user_id)
  {
    $this->db->sqlDelete('sys_session', array('user_id' => $user_id));
  }

}
?>