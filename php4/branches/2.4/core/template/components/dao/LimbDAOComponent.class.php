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

class LimbDAOComponent extends Component
{
  var $class_path;
  var $dao;
  var $targets;
  var $navigator_id;

  function setClassPath($class_path)
  {
    $this->class_path = $class_path;
  }

  function &_createDAO()
  {
    if ($this->dao)
      return $this->dao;

    $toolkit =& Limb :: toolkit();
    $this->dao =& $toolkit->createDAO($this->class_path);

    return $this->dao;
  }

  function setTargets($targets)
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
    $ds =& $this->_createDAO();
    return $ds->fetch();
  }

  function setNavigator($navigator_id)
  {
    $this->navigator_id = $navigator_id;
  }

  function process()
  {
    $dataset =& $this->getDataset();

    if($navigator =& $this->_getNavigatorComponent())
      $dataset->paginate($navigator);

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