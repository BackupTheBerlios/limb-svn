<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/TreeDecorator.class.php');

class SessionTree extends TreeDecorator
{
  function initializeExpandedParents()
  {
    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();
    $this->_tree->setExpandedParents($session->getReference('tree_expanded_parents'));
  }
}

?>