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
require_once(LIMB_DIR . '/core/db/DbConfig.class.php');
require_once(LIMB_DIR . '/core/util/ini_support.inc.php');

class IniBasedDbConfig extends DbConfig
{
  function IniBasedDbConfig($name)
  {
    parent :: DbConfig($name);

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getIni('common.ini');

    $this->driver = $ini->getOption('driver', 'DB');
    $this->host = $ini->getOption('host', 'DB');
    $this->database = $ini->getOption('database', 'DB');
    $this->user = $ini->getOption('user', 'DB');
    $this->password = $ini->getOption('password', 'DB');
  }
}

?>
