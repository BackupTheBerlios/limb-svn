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
require_once(LIMB_DIR . '/class/datasources/FetchSubBranchDatasource.class.php');

class NodeSelectDatasource extends FetchSubBranchDatasource
{
  function getDataset(&$counter, $params = array())
  {
    $params['depth'] = 1;

    if(Limb :: toolkit()->getRequest()->get('only_parents') == 'false')
      $params['only_parents'] = false;
    else
      $params['only_parents'] = true;

    $params['restrict_by_class'] = false;
    $params['path'] = $this->_processPath();

    return parent :: getDataset($counter, $params);
  }

  function _processPath()
  {
    $default_path = '/root/';

    if(!$path = Limb :: toolkit()->getRequest()->get('path'))
      return $default_path;

    if(strpos($path, '?') !== false)
    {
      if(!$path = Limb :: toolkit()->getTree()->getNodeByPath($path))
        return $default_path;
    }
    return $path;
  }

}



?>