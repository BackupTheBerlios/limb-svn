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

class LimbToolkit
{
  function define($key, $value){}
  function constant($key){}
  function createDBTable($table_name){}
  function createDAO($dao_path){}
  function createObject($object_path){}
  function createDataMapper($mapper_path){}
  function createBehaviour($behaviour_path){}
  function nextUID(){}
  function getDbConnection(){}
  function getTree(){}
  function getUser(){}
  function getINI($ini_path){}
  function flushINIcache(){}
  function getAuthorizer(){}
  function getAuthenticator(){}
  function getRequest(){}
  function getResponse(){}
  function getLocale(){}
  function getDataspace(){}
  function getCache(){}
  function getUOW(){}
  function switchDataspace($name){}
  function setView($view){}
  function getView(){}
  function getSession(){}
  //function translate();
}

?>
