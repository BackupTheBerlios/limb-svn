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
    $this->db_table =& $toolkit->createDBTable('StatIp');
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
    $this->db_table->insert(array(
        'id' => $this->getClientIp(),
        'time' => $stamp
      )
    );
  }

  function getClientIp()
  {
    return Ip :: encode(Sys :: clientIp());
  }

  function _getStatIpRecord()
  {
    $rs =& $this->db_table->select(array('id' => $this->getClientIp()));
    return $rs->getRow();
  }

  function _updateStatIpRecord($stamp)
  {
    $this->db_table->update(array('time' => $stamp),
                            array('id' => $this->getClientIp()));
  }
}

?>