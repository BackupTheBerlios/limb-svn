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

class PathDatasourceProcessorComponent extends Component
{
  var $translator;

  function process()
  {
    $translator =& $this->_getTranslator();
    $datasource =& $this->parent->getDataSource();
    if(!$node_id = $datasource->get('_node_id'))
      return;

    $datasource->set('path', $translator->getPathToNode($node_id));
  }

  function & _getTranslator()
  {
    if(is_object($this->translator))
      return $this->translator;

    $toolkit =& Limb :: toolkit();
    $this->translator =& $toolkit->getPath2IdTranslator();

    return $this->translator;
  }
}

?>