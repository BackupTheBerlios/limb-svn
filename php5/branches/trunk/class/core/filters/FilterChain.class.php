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
  protected $filters = array();
  protected $counter = 0;

  protected $request;
  protected $response;

  function __construct($request, $response)
  {
    $this->request = $request;
    $this->response = $response;
  }

  public function registerFilter($filter)
  {
    $this->filters[] = $filter;
  }

  public function hasFilter($filter_class)
  {
    foreach(array_keys($this->filters) as $key)
    {
      resolveHandle($this->filters[$key]);
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
      resolveHandle($this->filters[$this->counter]);
      $this->filters[$this->counter]->run($this, $this->request, $this->response);
    }
  }

  public function process()
  {
    $this->counter = 0;

    if(sizeof($this->filters) > 0)
    {
      resolveHandle($this->filters[0]);
      $this->filters[0]->run($this, $this->request, $this->response);
    }
  }

}

?>