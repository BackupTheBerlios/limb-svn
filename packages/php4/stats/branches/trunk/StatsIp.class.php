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
require_once(LIMB_DIR . '/core/date/Date.class.php');
require_once(LIMB_DIR . '/core/http/Ip.class.php');

class StatsIp
{
  var $db_table = null;

  function StatsIp()
  {
    $toolkit =& Limb :: toolkit();
    $this->db_table =& $toolkit->createDBTable('StatsIp');
  }

  function isNewToday($ip, $reg_time)
  {
    $ip = Ip :: encode($ip);

    if(($record = $this->_getStatIpRecord($ip)) === false)
    {
      $this->_insertStatIpRecord($reg_time, $ip);
      return true;
    }

    $ip_date = new Date();
    $ip_date->setByStamp($record['time']);
    $reg_date = new Date();
    $reg_date->setByStamp($reg_time);

    if($ip_date->dateToDays() < $reg_date->dateToDays())
    {
      $this->_updateStatIpRecord($reg_time, $ip);
      return true;
    }
    elseif($ip_date->dateToDays() > $reg_date->dateToDays()) //this shouldn't happen normally...
      return false;

    return false;
  }

  function _insertStatIpRecord($stamp, $ip)
  {
    $this->db_table->insert(array(
        'id' => $ip,
        'time' => $stamp
      )
    );
  }

  function _getStatIpRecord($ip)
  {
    $rs =& $this->db_table->select(array('id' => $ip));
    return $rs->getRow();
  }

  function _updateStatIpRecord($stamp, $ip)
  {
    $this->db_table->update(array('time' => $stamp),
                            array('id' => $ip));
  }
}

?>