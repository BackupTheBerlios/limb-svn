<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudDomainObjectDAO.class.php 27 2005-02-26 18:57:22Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/dao/DAO.class.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

class RequestUriPathElementsDAO // implements DAO
{
  function fetch()
  {
    $toolkit = Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri = $request->getUri();

    $elements = $uri->getPathElements();

    $result = array();
    $temp_uri = $uri;
    $temp_uri->removeQueryItems();
    $path = '';

    array_pop($elements);

    foreach($elements as $item)
    {
      $path .= $item . '/';

      $temp_uri->setPath($path);
      $result[]['uri'] = $temp_uri->toString();
    }

    $result = array_reverse($result);

    return new PagedArrayDataset($result);
  }
}

?>
