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

/**
* The dataspace_component does nothing other than extend component but is
* required to build the runtime component heirarchy, being the root component
*/
class DataspaceComponent extends Component
{
  public function registerDataset($dataset)
  {
    $this->import($dataset->export());
  }
}
?>