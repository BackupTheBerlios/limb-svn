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
class stats_ip
{
  protected $db = null;

  function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  public function is_new_host($reg_date)
  {
    if(($record = $this->_get_stat_ip_record()) === false)
    {
      $this->_insert_stat_ip_record($reg_date->get_stamp());
      return true;
    }

    $ip_date = new date();
    $ip_date->set_by_stamp($record['time']);

    if($ip_date->date_to_days() < $reg_date->date_to_days())
    {
      $this->_update_stat_ip_record($reg_date->get_stamp());
      return true;
    }
    elseif($ip_date->date_to_days() > $reg_date->date_to_days()) //this shouldn't happen normally...
      return false;

    return false;
  }

  protected function _insert_stat_ip_record($stamp)
  {
    $this->db->sql_insert('sys_stat_ip',
      array(
        'id' => $this->get_client_ip(),
        'time' => $stamp
      )
    );
  }

  public function get_client_ip()
  {
    return ip :: encode_ip(sys :: client_ip());
  }

  protected function _get_stat_ip_record()
  {
    $this->db->sql_select('sys_stat_ip', '*', array('id' => $this->get_client_ip()));
    return $this->db->fetch_row();
  }

  protected function _update_stat_ip_record($stamp)
  {
    $this->db->sql_update('sys_stat_ip',
      array('time' => $stamp),
      array('id' => $this->get_client_ip())
    );
  }
}

?>