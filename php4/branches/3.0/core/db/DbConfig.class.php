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

class DbConfig
{
  //used to uniquely identify db config
  var $name;

  var $driver;
  var $host;
  var $database;
  var $user;
  var $password;

  function DbConfig($name)
  {
    $this->name = $name;
  }

  //implements WACT db config
  function get($key)
  {
    return isset($this->$key) ? $this->$key : false;
  }
}

?>
