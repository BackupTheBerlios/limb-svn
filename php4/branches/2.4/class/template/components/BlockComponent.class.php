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
* The block tag can be used to show or hide the contents of the block.
* The block_component provides an API which allows the block to be shown
* or hidden at runtime.
*/
class BlockComponent extends Component
{
  /**
  * Whether the block is visible or not
  */
  protected $visible = true;
  /**
  * Called within the compiled template render function to determine
  * whether block should be displayed.
  */
  public function isVisible()
  {
    return $this->visible;
  }

  /**
  * Changes the block state to visible
  */
  public function show()
  {
    $this->visible = true;
  }

  /**
  * Changes the block state to invisible
  */
  public function hide()
  {
    $this->visible = false;
  }
}

?>