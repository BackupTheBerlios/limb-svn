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

class SessionDbDriver// implements SessionDriver
{
  var $db;
  var $user;

  function SessionDbDriver()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& new SimpleDb($toolkit->getDbConnection());
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
    $rs =& $this->db->select('sys_session', 'session_data', array('session_id' => "{$session_id}"));

    if($data = $rs->getValue())
      return $data;
    else
      return false;
  }

  function storageWrite($session_id, $value)
  {
    $rs =& $this->db->select('sys_session', 'session_id', array('session_id' => "{$session_id}"));

    $session_data = array('last_activity_time' => time(),
                          'session_data' => "{$value}");

    if($rs->getTotalRowCount() > 0)
      $this->db->update('sys_session', $session_data, array('session_id' => "{$session_id}"));
    else
    {
      $session_data['session_id'] = "{$session_id}";
      $session_data['user_id'] = $this->user->getId();

      $this->db->insert('sys_session', $session_data);
    }
  }

  function storageDestroy($session_id)
  {
    $this->db->delete('sys_session', array('session_id' => "{$session_id}"));
  }

  function storageGc($max_life_time)
  {
    $this->db->delete('sys_session', "last_activity_time < " . (time() - $max_life_time));
  }

  //$conn =& $this->db->getConnection();
  //$stmt = $conn->newStatement('DELETE FROM sys_session WHERE last_activity_time < :time');
  //$stmt->setInteger('time', time() - $max_life_time);
  //$stmt->execute();

  function storageDestroyUser($user_id)
  {
    $this->db->delete('sys_session', array('user_id' => (int)$user_id));
  }

}
?>