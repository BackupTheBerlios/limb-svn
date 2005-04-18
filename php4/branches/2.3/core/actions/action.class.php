<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/util/dataspace_registry.class.php');

class action
{
  var $name = '';

  var $dataspace = null;

  var $view = null;

  function action()
  {
    $this->name = $this->_define_dataspace_name();

    $this->dataspace =& dataspace_registry :: get($this->name);
  }

  function _define_dataspace_name()
  {
    return '';
  }

  function set_view(&$view)
  {
    $this->view =& $view;
  }

  function perform(&$request, &$response)
  {
    $request->set_status(REQUEST_STATUS_SUCCESS);
  }
}
?>