<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/core/object.class.php');
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

class request extends object
{
  const STATUS_DONT_TRACK = 0;

  const STATUS_SUCCESS_MASK = 15;
  const STATUS_PROBLEM_MASK = 240;

  const STATUS_SUCCESS = 1;
  const STATUS_FORM_SUBMITTED = 2;
  const STATUS_FORM_DISPLAYED = 4;

  const STATUS_FORM_NOT_VALID = 16;
  const STATUS_FAILURE = 32;
  
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
      $request = $this->_strip_http_slashes($request);

    foreach ($request as $k => $v)
      $this->set($k, $v); 
      
    $this->status = request :: STATUS_SUCCESS; 
  }
  
  protected function _strip_http_slashes($data, $result=array())
  {		
  	foreach($data as $k => $v)
  	{
    	if(is_array($v))
    		$result[$k] = $this->_strip_http_slashes($v);
  		else
  			$result[$k] = stripslashes($v);
  	}
  			
  	return $result;
  } 
  
  public function get_uri()
  {
    if($this->uri === null)
      $this->_init_uri();
    
    return $this->uri;
  }
  
  protected function _init_uri()
  {
    $this->uri = new uri($_SERVER['REQUEST_URI']);
  }
  
  public function set_status($status)
  {
    $this->status = $status;
  }
  
  public function get_status()
  {
    return $this->status;
  }
  
	public function is_success()
	{
		return ($this->status & request :: STATUS_SUCCESS_MASK);
	}

	public function is_problem()
	{
		return ($this->status & request :: STATUS_PROBLEM_MASK);
	}  
}

?>