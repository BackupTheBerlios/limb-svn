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
require_once(LIMB_DIR . '/class/core/Object.class.php');

class Request extends Object
{
  static protected $instance = null;

  protected $status;
  protected $uri;

  function __construct()
  {
    parent :: __construct();

    global $HTTP_POST_VARS, $HTTP_GET_VARS;

    // for different PHP versions
    if (isset($_GET))
      $request = array_merge($_GET, $_POST);
    else
      $request = array_merge($HTTP_GET_VARS, $HTTP_POST_VARS);

    if(ini_get('magic_quotes_gpc'))
      $request = $this->_stripHttpSlashes($request);

    foreach ($request as $k => $v)
      $this->set($k, $v);
  }

  protected function _stripHttpSlashes($data, $result=array())
  {
    foreach($data as $k => $v)
    {
      if(is_array($v))
        $result[$k] = $this->_stripHttpSlashes($v);
      else
        $result[$k] = stripslashes($v);
    }

    return $result;
  }

  public function getUri()
  {
    if($this->uri === null)
      $this->_initUri();

    return $this->uri;
  }

  protected function _initUri()
  {
    $this->uri = new Uri($_SERVER['REQUEST_URI']);
  }
}

?>