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
require_once(LIMB_DIR . '/core/permissions/JIPProcessor.class.php');

class JIPComponent extends Component
{
  var $processor;

  function process()
  {
    $jip_processor =& $this->_getJIPProcessor();
    $jip_processor->process($this->parent->getDataSource());
  }

  function & _getJIPProcessor()
  {
    if(is_object($this->processor))
      return $this->processor;

    include_once(LIMB_DIR . '/core/permissions/JIPProcessor.class.php');
    $this->processor = new JIPProcessor();

    return $this->processor;
  }
}

?>