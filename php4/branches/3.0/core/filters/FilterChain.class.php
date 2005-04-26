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
  var $context;

  function FilterChain(&$request, &$response, &$context)
  {
    $this->request =& $request;
    $this->response =& $response;
    $this->context =& $context;
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
      $filter->run($this, $this->request, $this->response, $this->context);
    }
  }

  function process()
  {
    $this->counter = -1;
    $this->next();
  }

}

?>