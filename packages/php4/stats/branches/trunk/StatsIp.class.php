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
class StatsIp
{
  var $db = null;

  function StatsIp()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function isNewHost($reg_date)
  {
    if(($record = $this->_getStatIpRecord()) === false)
    {
      $this->_insertStatIpRecord($reg_date->getStamp());
      return true;
    }

    $ip_date = new Date();
    $ip_date->setByStamp($record['time']);

    if($ip_date->dateToDays() < $reg_date->dateToDays())
    {
      $this->_updateStatIpRecord($reg_date->getStamp());
      return true;
    }
    elseif($ip_date->dateToDays() > $reg_date->dateToDays()) //this shouldn't happen normally...
      return false;

    return false;
  }

  function _insertStatIpRecord($stamp)
  {
    $this->db->sqlInsert('sys_stat_ip',
      array(
        'id' => $this->getClientIp(),
        'time' => $stamp
      )
    );
  }

  function getClientIp()
  {
    return Ip :: encodeIp(Sys :: clientIp());
  }

  function _getStatIpRecord()
  {
    $this->db->sqlSelect('sys_stat_ip', '*', array('id' => $this->getClientIp()));
    return $this->db->fetchRow();
  }

  function _updateStatIpRecord($stamp)
  {
    $this->db->sqlUpdate('sys_stat_ip',
      array('time' => $stamp),
      array('id' => $this->getClientIp())
    );
  }
}

?>