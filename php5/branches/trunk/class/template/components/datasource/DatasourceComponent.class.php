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
require_once(LIMB_DIR . '/class/core/array_dataset.class.php');
require_once(LIMB_DIR . '/class/template/component.class.php');

class datasource_component extends component
{
  protected $datasource_path;
  protected $datasource;
  protected $targets;
  protected $navigator_id;
  protected $parameters = array();

  public function set_datasource_path($datasource_path)
  {
    $this->datasource_path = $datasource_path;
  }

  protected function _get_datasource()
  {
    if ($this->datasource)
      return $this->datasource;

    $this->datasource = Limb :: toolkit()->getDatasource($this->datasource_path);

    foreach($this->parameters as $key => $value)
    {
      $method = 'set_' . $key;

      if(method_exists($this->datasource, $method))
        $this->datasource->$method($value);
    }
    return $this->datasource;
  }

  protected function _set_targets($targets)
  {
    if(is_array($targets))
      $this->targets = $targets;
    elseif(is_string($targets))
    {
      $this->targets = array();

      $pieces = explode(',', $targets);
      foreach($pieces as $piece)
        $this->targets[] = trim($piece);

    }
  }

  public function get_dataset()
  {
    if ($result = $this->_get_datasource()->fetch())
      return new array_dataset($result);
    else
      return new empty_dataset();
  }

  public function set_parameter($name, $value)
  {
    if($name == 'order')
      $this->_set_order_parameters($value);
    elseif($name == 'limit')
      $this->_set_limit_parameters($value);
    else
      $this->parameters[$name] = $value;
  }

  public function get_parameter($name)
  {
    if(isset($this->parameters[$name]))
      return $this->parameters[$name];
  }

  protected function _set_limit_parameters($limit_string)
  {
    $arr = explode(',', $limit_string);

    if(empty($arr[0]))
      return;

    $this->parameters['limit'] = (int)$arr[0];

    if(!empty($arr[1]))
      $this->parameters['offset'] = (int)$arr[1];
  }

  protected function _set_order_parameters($order_string)
  {
    $order_items = explode(',', $order_string);
    $order_pairs = array();
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);
      if(!$field = trim($arr[0]))
        continue;

      if(empty($arr[1]))
      {
        $order_pairs[$field] = 'ASC';
        continue;
      }
      else
        $sort = trim($arr[1]);

      if(strtolower($sort) == 'asc' ||
         strtolower($sort) == 'desc'||
         strtolower($sort) == 'rand()')
        $order_pairs[$field] = strtoupper($sort);
      else
        $order_pairs[$field] = 'ASC';
    }

    if($order_pairs)
      $this->parameters['order'] = $order_pairs;
  }

  public function setup_navigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;

    if(!$navigator = $this->_get_navigator_component())
      return null;

    $limit = $navigator->get_items_per_page();
    $this->set_parameter('limit', $limit);

    $navigator_id = 'page_' . $navigator->get_server_id();

    $request = Limb :: toolkit()->getRequest();

    if ($request->has_attribute($navigator_id))
    {
      $offset = ((int)$request->get($navigator_id)-1)*$limit;
      $this->set_parameter('offset', $offset);
    }

    $navigator->set_total_items($this->_get_datasource()->count_total());
  }

  public function setup_targets($targets)
  {
    $this->_set_targets($targets);

    $dataset = $this->get_dataset();
    foreach($this->targets as $target)
    {
      if($target_component = $this->parent->find_child($target))
      {
        $target_component->register_dataset($dataset);
      }
      else
      {
        throw new WactException('target component not found',
                                array('target' => $target));
      }
    }
  }

  protected function _get_navigator_component()
  {
    if (!$this->navigator_id)
      return null;

    if(!$navigator = $this->parent->find_child($this->navigator_id))
      return null;

    return $navigator;
  }
}
?>