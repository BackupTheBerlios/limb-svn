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

class FilterChain
{
  var $filters = array();
  var $counter = -1;

  var $request;
  var $response;

  function FilterChain(&$request, &$response)
  {
    $this->request =& $request;
    $this->response =& $response;
  }

  function registerFilter(&$filter)
  {
    $this->filters[] =& $filter;
  }

  function hasFilter($filter_class)
  {
    foreach(array_keys($this->filters) as $key)
    {
      $this->filters[$key] =& Handle :: resolve($this->filters[$key]);
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
      $filter =& Handle :: resolve($this->filters[$this->counter]);
      $filter->run($this, $this->request, $this->response);
    }
  }

  function process()
  {
    $this->counter = -1;
    $this->next();
  }

}

?>