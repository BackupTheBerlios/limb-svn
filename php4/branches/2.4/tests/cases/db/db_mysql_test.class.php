<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/db_driver_test.class.php');

class db_mysql_test extends db_driver_test
{
  function &_create_db_driver()
  {
    return db_factory :: instance();
  }
}
?>