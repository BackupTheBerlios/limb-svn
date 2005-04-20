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
  var $processor;

  function process()
  {
    $processor =& $this->_getPathProcessor();
    $datasource =& $this->parent->getDataSource();

    $processor->process($datasource);
  }

  function & _getPathProcessor()
  {
    if(is_object($this->processor))
      return $this->processor;

    require_once(LIMB_DIR . '/core/dao/processors/PathRecordProcessor.class.php');
    $this->processor = new PathRecordProcessor();

    return $this->processor;
  }
}

?>