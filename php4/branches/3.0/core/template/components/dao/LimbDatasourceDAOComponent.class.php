<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbDAOComponent.class.php 1094 2005-02-08 13:09:14Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class LimbDatasourceDAOComponent extends Component
{
  var $class_path;
  var $dao;
  var $targets;

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

  function process()
  {
    $ds =& $this->_createDAO();
    $datasource =& $ds->fetch();

    foreach($this->targets as $target)
    {
      if($target_component =& $this->parent->findChild($target))
      {
        $target_component->registerDataSource($datasource);
      }
      else
      {
        return throw_error(new WactException('target component not found',
                                array('target' => $target)));
      }
    }
  }
}
?>