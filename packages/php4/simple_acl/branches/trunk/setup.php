<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: setup.php 913 2004-11-22 12:32:48Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/SimpleACLBaseToolkit.class.php');

Limb :: registerToolkit(new SimpleACLBaseToolkit(), 'SimpleACL');

$PACKAGE_NAME = 'LIMB_SIMPLE_ACL';

?>