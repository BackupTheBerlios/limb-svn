<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: db_mysql_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/db_driver_test.class.php');

SimpleTestOptions :: ignore('db_postgres_test');

class db_postgres_test extends db_driver_test
{
  function _create_db_driver()
  {
    return db_factory :: instance('postgres');
  }
}
?>