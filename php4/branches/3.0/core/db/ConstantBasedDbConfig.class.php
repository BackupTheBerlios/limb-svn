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

class ConstantBasedDbConfig extends DbConfig
{
  function ConstantBasedDbConfig($name)
  {
    parent :: DbConfig($name);

    $this->driver = DB_DRIVER;
    $this->host = DB_HOST;
    $this->database = DB_NAME;
    $this->user = DB_USER;
    $this->password = DB_PASSWORD;
  }
}

?>
