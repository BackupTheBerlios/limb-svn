<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/object.class.php');
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');

define('REQUEST_STATUS_DONT_TRACK', 0);

define('REQUEST_STATUS_SUCCESS_MASK', 15);
define('REQUEST_STATUS_PROBLEM_MASK', 240);

define('REQUEST_STATUS_SUCCESS', 1);
define('REQUEST_STATUS_FORM_SUBMITTED', 2);
define('REQUEST_STATUS_FORM_DISPLAYED', 4);

define('REQUEST_STATUS_FORM_NOT_VALID', 16);
define('REQUEST_STATUS_FAILURE', 32);

class request extends object
{
  var $status;
  var $uri;

  function request()
  {
    parent :: object();

    $this->_fill_request_properties();

    $this->status = REQUEST_STATUS_SUCCESS;
  }

  function _fill_request_properties()
  {
    $request = complex_array :: array_merge($_GET, $_POST);

    if(ini_get('magic_quotes_gpc'))
      $request = $this->_strip_http_slashes($request);

    if($_FILES)
      $request = complex_array :: array_merge($request, $this->_get_uploaded_files());

    foreach ($request as $k => $v)
      $this->set_attribute($k, $v);
  }

  function _get_uploaded_files()
  {
    include_once(LIMB_DIR . '/core/request/uploaded_files_parser.class.php');
    $parser = new uploaded_files_parser();
    return $parser->parse($_FILES);
  }

  function _strip_http_slashes($data, $result=array())
  {
    foreach($data as $k => $v)
      if(is_array($v))
        $result[$k] = $this->_strip_http_slashes($v);
      else
        $result[$k] = stripslashes($v);

    return $result;
  }

  function & instance()
  {
    $obj =& instantiate_object('request');
    return $obj;
  }

  function & get_uri()
  {
    if($this->uri === null)
      $this->_init_uri();

    return $this->uri;
  }

  function _init_uri()
  {
    $this->uri = new uri($_SERVER['REQUEST_URI']);
  }

  function set_status($status)
  {
    $this->status = $status;
  }

  function get_status()
  {
    return $this->status;
  }

  function is_success()
  {
    return ($this->status & REQUEST_STATUS_SUCCESS_MASK);
  }

  function is_problem()
  {
    return ($this->status & REQUEST_STATUS_PROBLEM_MASK);
  }
}

?>