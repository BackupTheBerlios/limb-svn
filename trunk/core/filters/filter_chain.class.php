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

class filter_chain
{
  var $filters = array();
  var $counter = 0;
  
  var $request;
  var $response;
  
  function filter_chain(&$request, &$response)
  {
    $this->request =& $request;
    $this->response =& $response;
  }
  
  function register_filter(&$filter)
  {
    $this->filters[] =& $filter;
  }
  
  function has_filter($filter_class)
  {
    foreach($this->filters as $filter)
    {
      if(get_class($filter) == strtolower($filter_class))
        return true;
    }
    
    return false;
  }
  
  function next() 
  { 
    $this->counter++;
    
    if(isset($this->filters[$this->counter]))
    {
      $this->filters[$this->counter]->run($this, $this->request, $this->response); 
    }
  } 
   
  function process() 
  { 
    $this->counter = 0;
    
    if(sizeof($this->filters) > 0)
    {
      $this->filters[0]->run($this, $this->request, $this->response);
    }
  }    
  
}

?>