<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/session_driver.interface.php');
require_once(LIMB_DIR . '/class/lib/system/sys.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

class session_db_driver implements session_driver
{
  protected $db;
  protected $user;
  
  function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
    $this->user = Limb :: toolkit()->getUser();
  }
        
  public function storage_open()
  {
    return true;
  }
  
  public function storage_close()
  {
    return true;
  }
  
  public function storage_read($session_id)
  {
    $this->db->sql_select('sys_session', 'session_data', array('session_id' => $session_id));
    
    if($data = $this->db->fetch_row())
    {      
      return $data['session_data'];
    }
    else
      return false;
  }
  
  public function storage_write($session_id, $value)
  {
    $this->db->sql_select('sys_session', 'session_id', array('session_id' => $session_id));
    
    $session_data = array('last_activity_time' => time(),
                          'session_data' => "{$value}");
    
    if($this->db->fetch_row())
      $this->db->sql_update('sys_session', $session_data, array('session_id' => $session_id));
    else
    {
      $session_data['session_id'] = "{$session_id}";  //type juggling to string
      $session_data['user_id'] = $this->user->get_id();
      
      $this->db->sql_insert('sys_session', $session_data);
    }
  }
  
  public function storage_destroy($session_id)
  {
    $this->db->sql_delete('sys_session', array('session_id' => $session_id));
  }
  
  public function storage_gc($max_life_time)
  {  
    $this->db->sql_delete('sys_session', "last_activity_time < ". (time() - $max_life_time));
  }
  
  public function storage_destroy_user($user_id)
  {
    $this->db->sql_delete('sys_session', array('user_id' => $user_id));
  }
  
}  
?>