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
  protected $filters = array();
  protected $counter = 0;
  
  protected $request;
  protected $response;
  
  function __construct($request, $response)
  {
    $this->request = $request;
    $this->response = $response;
  }
  
  public function register_filter($filter)
  {
    $this->filters[] = $filter;
  }
  
  public function has_filter($filter_class)
  {
    foreach(array_keys($this->filters) as $key)
    {
      resolve_handle($this->filters[$key]);
      if(get_class($this->filters[$key]) == $filter_class)
        return true;
    }
    
    return false;
  }
  
  public function next() 
  { 
    $this->counter++;
    
    if(isset($this->filters[$this->counter]))
    {
      resolve_handle($this->filters[$this->counter]);
      $this->filters[$this->counter]->run($this, $this->request, $this->response); 
    }
  } 
   
  public function process() 
  { 
    $this->counter = 0;
    
    if(sizeof($this->filters) > 0)
    {
      resolve_handle($this->filters[0]);
      $this->filters[0]->run($this, $this->request, $this->response);
    }
  }    
  
}

?>