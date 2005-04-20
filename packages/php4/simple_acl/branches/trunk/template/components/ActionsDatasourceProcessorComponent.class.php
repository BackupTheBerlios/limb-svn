<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: JIPComponent.class.php 1186 2005-03-23 09:47:34Z seregalimb $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');

class ActionsDatasourceProcessorComponent extends Component
{
  var $authorizer;

  function process()
  {
    $authorizer =& $this->_getAuthorizer();

    $datasource =& $this->parent->getDataSource();

    if(!$path = $datasource->get('_node_path'))
      return;

    if(!$service_name = $datasource->get('_service_name'))
      return;

    if($actions = $authorizer->getAccessibleActions($path, $service_name))
      $datasource->set('actions', $actions);
  }

  function & _getAuthorizer()
  {
    if(is_object($this->authorizer))
      return $this->authorizer;

    $toolkit =& Limb :: toolkit('SimpleACL');
    $this->authorizer =& $toolkit->getAuthorizer();

    return $this->authorizer;
  }
}

?>