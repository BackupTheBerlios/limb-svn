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
    foreach(array_keys($this->filters) as $key)
    {
      resolve_handle($this->filters[$key]);
      if(get_class($this->filters[$key]) == strtolower($filter_class))
        return true;
    }

    return false;
  }

  function next()
  {
    $this->counter++;

    if(isset($this->filters[$this->counter]))
    {
      resolve_handle($this->filters[$this->counter]);
      $this->filters[$this->counter]->run($this, $this->request, $this->response);
    }
  }

  function process()
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