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
require_once(LIMB_DIR . '/core/db/DbConnectionConfig.class.php');

class IniBasedDbConnectionConfig extends DbConnectionConfig
{
  function IniBasedDbConnectionConfig($name)
  {
    parent :: DbConnectionConfig($name);

    $ini =& getIni('common.ini');

    $this->driver = $ini->getOption('driver', 'DB');
    $this->host = $ini->getOption('host', 'DB');
    $this->database = $ini->getOption('database', 'DB');
    $this->user = $ini->getOption('user', 'DB');
    $this->password = $ini->getOption('password', 'DB');
  }
}

?>
