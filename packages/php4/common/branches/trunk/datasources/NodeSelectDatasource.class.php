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

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $tree =& $toolkit->getTree();

    if($request->get('only_parents') == 'false')
      $params['only_parents'] = false;
    else
      $params['only_parents'] = true;

    $params['restrict_by_class'] = false;
    $params['path'] = $this->_processPath($request, $tree);

    return parent :: getDataset($counter, $params);
  }

  function _processPath(&$request, &$tree)
  {
    $default_path = '/root/';

    if(!$path = $request->get('path'))
      return $default_path;

    if(strpos($path, '?') !== false)
    {
      if(!$path = $tree->getNodeByPath($path))
        return $default_path;
    }
    return $path;
  }

}



?>