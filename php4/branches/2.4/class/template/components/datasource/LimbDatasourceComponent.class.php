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
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class LimbDatasourceComponent extends Component
{
  var $class_path;
  var $datasource;
  var $targets;
  var $navigator_id;

  function setClassPath($class_path)
  {
    $this->class_path = $class_path;
  }

  function &_getDatasource()
  {
    if ($this->datasource)
      return $this->datasource;

    $toolkit =& Limb :: toolkit();
    $this->datasource =& $toolkit->getDatasource($this->class_path);

    return $this->datasource;
  }

  function _setTargets($targets)
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

  function &getDataset()
  {
    $ds =& $this->_getDatasource();
    return $ds->fetch();
  }

  function setParameter($name, $value)
  {
    if($name == 'order')
      $this->_setOrderParameters($value);
    elseif($name == 'limit')
      $this->_setLimitParameters($value);
    else
      $this->_setDatasourceParameter($name, $value);
  }

  function _setLimitParameters($limit_string)
  {
    $arr = explode(',', $limit_string);

    if(empty($arr[0]))
      return;

    $this->_setDatasourceParameter('limit', (int)$arr[0]);

    if(!empty($arr[1]))
      $this->_setDatasourceParameter('offset', (int)$arr[1]);
  }

  function _setOrderParameters($order_string)
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
      $this->_setDatasourceParameter('order', $order_pairs);
  }

  function setupNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;

    if(!$navigator =& $this->_getNavigatorComponent())
      return null;

    $ds =& $this->_getDatasource();
    $navigator->setTotalItems($ds->countTotal());
    $navigator->reset();//???

    $this->_setDatasourceParameter('limit', $navigator->getItemsPerPage());
    $this->_setDatasourceParameter('offset', $navigator->getDisplayedPageBeginItem());
  }

  function _setDatasourceParameter($parameter, $value)
  {
    $ds =& $this->_getDatasource();

    $method = 'set' . ucfirst($parameter);

    if(method_exists($ds, $method))
      $ds->$method($value);
  }

  function setupTargets($targets)
  {
    $this->_setTargets($targets);

    $dataset =& $this->getDataset();
    foreach($this->targets as $target)
    {
      if($target_component =& $this->parent->findChild($target))
      {
        $target_component->registerDataSet($dataset);
      }
      else
      {
        return throw(new WactException('target component not found',
                                array('target' => $target)));
      }
    }
  }

  function &_getNavigatorComponent()
  {
    if (!$this->navigator_id)
      return null;

    if(!$navigator =& $this->parent->findChild($this->navigator_id))
      return null;

    return $navigator;
  }
}
?>