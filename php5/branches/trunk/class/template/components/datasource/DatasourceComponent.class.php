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
require_once(LIMB_DIR . '/class/core/ArrayDataset.class.php');
require_once(LIMB_DIR . '/class/template/Component.class.php');

class DatasourceComponent extends Component
{
  protected $datasource_path;
  protected $datasource;
  protected $targets;
  protected $navigator_id;
  protected $parameters = array();

  public function setDatasourcePath($datasource_path)
  {
    $this->datasource_path = $datasource_path;
  }

  protected function _getDatasource()
  {
    if ($this->datasource)
      return $this->datasource;

    $this->datasource = Limb :: toolkit()->getDatasource($this->datasource_path);

    foreach($this->parameters as $key => $value)
    {
      $method = 'set' . ucfirst($key);

      if(method_exists($this->datasource, $method))
        $this->datasource->$method($value);
    }
    return $this->datasource;
  }

  protected function _setTargets($targets)
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

  public function getDataset()
  {
    if ($result = $this->_getDatasource()->fetch())
      return new ArrayDataset($result);
    else
      return new EmptyDataset();
  }

  public function setParameter($name, $value)
  {
    if($name == 'order')
      $this->_setOrderParameters($value);
    elseif($name == 'limit')
      $this->_setLimitParameters($value);
    else
      $this->parameters[$name] = $value;
  }

  public function getParameter($name)
  {
    if(isset($this->parameters[$name]))
      return $this->parameters[$name];
  }

  protected function _setLimitParameters($limit_string)
  {
    $arr = explode(',', $limit_string);

    if(empty($arr[0]))
      return;

    $this->parameters['limit'] = (int)$arr[0];

    if(!empty($arr[1]))
      $this->parameters['offset'] = (int)$arr[1];
  }

  protected function _setOrderParameters($order_string)
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
         strtolower($sort) == 'desc' ||
         strtolower($sort) == 'rand()')
        $order_pairs[$field] = strtoupper($sort);
      else
        $order_pairs[$field] = 'ASC';
    }

    if($order_pairs)
      $this->parameters['order'] = $order_pairs;
  }

  public function setupNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;

    if(!$navigator = $this->_getNavigatorComponent())
      return null;

    $limit = $navigator->getItemsPerPage();
    $this->setParameter('limit', $limit);

    $navigator_id = 'page_' . $navigator->getServerId();

    $request = Limb :: toolkit()->getRequest();

    if ($request->hasAttribute($navigator_id))
    {
      $offset = ((int)$request->get($navigator_id)-1)*$limit;
      $this->setParameter('offset', $offset);
    }

    $navigator->setTotalItems($this->_getDatasource()->countTotal());
  }

  public function setupTargets($targets)
  {
    $this->_setTargets($targets);

    $dataset = $this->getDataset();
    foreach($this->targets as $target)
    {
      if($target_component = $this->parent->findChild($target))
      {
        $target_component->registerDataset($dataset);
      }
      else
      {
        throw new WactException('target component not found',
                                array('target' => $target));
      }
    }
  }

  protected function _getNavigatorComponent()
  {
    if (!$this->navigator_id)
      return null;

    if(!$navigator = $this->parent->findChild($this->navigator_id))
      return null;

    return $navigator;
  }
}
?>